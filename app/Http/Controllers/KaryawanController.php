<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Karyawan;
use App\Models\Kolaborasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KaryawanController extends Controller
{
    /**
     * Display employee list for a specific branch (kolaborasi).
     */
    public function index($id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);

        // Karyawan currently assigned to this branch
        $karyawans = Karyawan::where('kolaborasi_id', $id_kolaborasi)
            ->where('peran', '!=', 'Admin Global')
            ->orderBy('nama_karyawan')
            ->get();

        // Karyawan assigned to other branches or unassigned
        $otherKaryawans = Karyawan::where(function ($q) use ($id_kolaborasi) {
            $q->where('kolaborasi_id', '!=', $id_kolaborasi)
                ->where('peran', '!=', 'Admin Global')
                ->orWhereNull('kolaborasi_id');
        })
            ->orderBy('nama_karyawan')
            ->get();

        return view('pages.cabang.menu.karyawan.karyawan-menu', compact('kolaborasi', 'karyawans', 'otherKaryawans'));
    }

    /**
     * Show the employee creation form.
     */
    public function create($id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);

        return view('pages.cabang.menu.karyawan.karyawan-create', compact('kolaborasi'));
    }

    /**
     * Store a new employee and their user account.
     */
    public function store(Request $request, $id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);

        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'nik' => 'nullable|string|size:16|unique:karyawans,nik',
            'no_telp' => 'required|string|max:20|unique:karyawans,no_telp|unique:users,phone',
            'email' => 'nullable|email|max:100|unique:karyawans,email',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'peran' => 'required|in:Terapis,Admin Kolaborasi,Admin Global',
            'tanggal_bergabung' => 'nullable|date',
            'status_karyawan' => 'required|in:Aktif,Tidak Aktif,Resign,PHK',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::transaction(function () use ($request, $kolaborasi) {
            $userRole = match ($request->peran) {
                'Terapis' => UserRole::THERAPIST,
                'Admin Kolaborasi' => UserRole::ADMIN_KOLABORASI,
                'Admin Global' => UserRole::ADMIN_GLOBAL,
            };

            // Password default: format d-m-Y based on birthday
            $dob = Carbon::parse($request->tanggal_lahir)->format('d-m-Y');

            $user = User::create([
                'name' => $request->nama_karyawan,
                'phone' => $request->no_telp,
                'password' => Hash::make($dob),
                'role' => $userRole,
            ]);

            $fotoPath = null;
            $fotoMime = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('karyawan-photos', 'public');
                $fotoMime = $request->file('foto')->getClientMimeType();
            }

            Karyawan::create([
                'user_id' => $user->id,
                'kode_karyawan' => 'KRY-'.strtoupper(Str::random(5)),
                'nik' => $request->nik,
                'nama_karyawan' => $request->nama_karyawan,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                'peran' => $request->peran,
                'tanggal_bergabung' => $request->tanggal_bergabung,
                'kolaborasi_id' => $kolaborasi->id,
                'status_karyawan' => $request->status_karyawan,
                'foto_path' => $fotoPath,
                'foto_mime' => $fotoMime,
            ]);
        });

        return redirect()
            ->route('admin-global.karyawan', $id_kolaborasi)
            ->with('success', 'Karyawan baru berhasil didaftarkan dan dipetakan ke cabang ini!');
    }

    /**
     * Show the edit / detail page of an employee.
     */
    public function detail($id_kolaborasi, $id_karyawan)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);
        $karyawan = Karyawan::where('kode_karyawan', $id_karyawan)->firstOrFail();
        $branches = Kolaborasi::orderBy('nama_kolaborasi')
            ->get(['id', 'nama_kolaborasi']);

        return view('pages.cabang.menu.karyawan.karyawan-detail', compact('kolaborasi', 'karyawan', 'branches'));
    }

    /**
     * Update employee info and branch mapping.
     */
    public function update(Request $request, $id_kolaborasi, $id_karyawan)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);
        $karyawan = Karyawan::findOrFail($id_karyawan);

        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'nik' => 'nullable|string|size:16|unique:karyawans,nik,'.$karyawan->id,
            'no_telp' => 'required|string|max:20|unique:karyawans,no_telp,'.$karyawan->id.'|unique:users,phone,'.$karyawan->user_id,
            'email' => 'nullable|email|max:100|unique:karyawans,email,'.$karyawan->id,
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'peran' => 'required|in:Terapis,Admin Kolaborasi,Admin Global',
            'tanggal_bergabung' => 'nullable|date',
            'status_karyawan' => 'required|in:Aktif,Tidak Aktif,Resign,PHK',
            'kolaborasi_id' => 'required|exists:kolaborasi,id',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::transaction(function () use ($request, $karyawan) {
            $userRole = match ($request->peran) {
                'Terapis' => UserRole::THERAPIST,
                'Admin Kolaborasi' => UserRole::ADMIN_KOLABORASI,
                'Admin Global' => UserRole::ADMIN_GLOBAL,
            };

            // Update user details
            $user = $karyawan->user;
            if ($user) {
                $user->update([
                    'name' => $request->nama_karyawan,
                    'phone' => $request->no_telp,
                    'role' => $userRole,
                ]);
            }

            // Photo processing
            $fotoPath = $karyawan->foto_path;
            $fotoMime = $karyawan->foto_mime;
            if ($request->hasFile('foto')) {
                if ($karyawan->foto_path) {
                    Storage::disk('public')->delete($karyawan->foto_path);
                }
                $fotoPath = $request->file('foto')->store('karyawan-photos', 'public');
                $fotoMime = $request->file('foto')->getClientMimeType();
            }

            $karyawan->update([
                'nik' => $request->nik,
                'nama_karyawan' => $request->nama_karyawan,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                'peran' => $request->peran,
                'tanggal_bergabung' => $request->tanggal_bergabung,
                'kolaborasi_id' => $request->kolaborasi_id,
                'status_karyawan' => $request->status_karyawan,
                'foto_path' => $fotoPath,
                'foto_mime' => $fotoMime,
            ]);
        });

        return redirect()
            ->route('admin-global.karyawan', $id_kolaborasi)
            ->with('success', 'Informasi karyawan berhasil diperbarui!');
    }

    /**
     * Remove employee from database.
     */
    public function destroy($id_kolaborasi, $id_karyawan)
    {
        $karyawan = Karyawan::findOrFail($id_karyawan);

        DB::transaction(function () use ($karyawan) {
            if ($karyawan->foto_path) {
                Storage::disk('public')->delete($karyawan->foto_path);
            }

            $user = $karyawan->user;
            if ($user) {
                $user->delete(); // This deletes Karyawan through CASCADE trigger, but doing user delete directly is safer
            } else {
                $karyawan->delete();
            }
        });

        return redirect()
            ->route('admin-global.karyawan', $id_kolaborasi)
            ->with('success', 'Karyawan berhasil dihapus dari sistem!');
    }

    /**
     * Map/Assign an employee to the specified branch.
     */
    public function mapToCabang(Request $request, $id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);

        // Validasi input array
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:karyawans,id',
        ]);

        // Update kolaborasi_id untuk semua karyawan yang dipilih
        $karyawan = Karyawan::whereIn('id', $request->employee_ids)->update([
            'kolaborasi_id' => $kolaborasi->id,
        ]);

        $count = count($request->employee_ids);

        return redirect()
            ->route('admin-global.karyawan', $id_kolaborasi)
            ->with('success', "{$count} karyawan berhasil dipetakan ke {$kolaborasi->nama_kolaborasi}!");
    }
}
