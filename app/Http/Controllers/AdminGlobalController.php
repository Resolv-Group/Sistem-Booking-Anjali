<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Karyawan;
use App\Models\Kolaborasi;
use App\Models\User;
use App\Models\Pasien;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminGlobalController extends Controller
{
    /**
     * Display the Admin Global Dashboard.
     */
    public function dashboard()
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        // 1. Total Kolaborasi
        $totalKolaborasi = Kolaborasi::count();

        // 2. Total Terapis
        $totalTerapis = Karyawan::where('peran', 'Terapis')
            ->where('status_karyawan', 'Aktif')
            ->count();

        // 3. Total Karyawan
        $totalKaryawan = Karyawan::count();

        // 4. Total Pasien
        $totalPasien = Pasien::count();

        // 5. Booking Hari Ini
        $bookingHariIni = Booking::whereHas('session', function ($query) use ($today) {
            $query->where('tanggal_sesi', $today);
        })
        ->whereIn('status', ['approved', 'completed'])
        ->count();

        // 6. Booking Bulan Ini
        $bookingBulanIni = Booking::whereHas('session', function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('tanggal_sesi', [$startOfMonth, $endOfMonth]);
        })
        ->whereIn('status', ['approved', 'completed'])
        ->count();

        // 7. Booking Chart Data (last 30 days)
        $start = now()->subDays(29)->toDateString();
        $end = now()->toDateString();

        $bookingsData = Booking::whereIn('booking.status', ['approved', 'completed'])
            ->whereHas('session', function ($query) use ($start, $end) {
                $query->whereBetween('tanggal_sesi', [$start, $end]);
            })
            ->join('terapis_sesi', 'booking.terapis_sesi_id', '=', 'terapis_sesi.id')
            ->selectRaw('terapis_sesi.tanggal_sesi, count(*) as count')
            ->groupBy('terapis_sesi.tanggal_sesi')
            ->orderBy('terapis_sesi.tanggal_sesi')
            ->pluck('count', 'tanggal_sesi')
            ->toArray();

        $chartLabels = [];
        $chartValues = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $chartLabels[] = now()->subDays($i)->format('d M');
            $chartValues[] = $bookingsData[$date] ?? 0;
        }

        return view('pages.dashboard.admin-global', compact(
            'totalKolaborasi',
            'totalTerapis',
            'totalKaryawan',
            'totalPasien',
            'bookingHariIni',
            'bookingBulanIni',
            'chartLabels',
            'chartValues'
        ));
    }

    /**
     * Admin Management view.
     */
    public function kelolaAdmin(Request $request)
    {
        $search = $request->query('search', '');

        $admins = Karyawan::whereIn('peran', ['Admin Global', 'Admin Kolaborasi'])
            ->with(['user', 'kolaborasi'])
            ->orderBy('nama_karyawan')
            ->get();

        $branches = Kolaborasi::orderBy('nama_kolaborasi')->get(['id', 'nama_kolaborasi']);

        $adminSearchData = $admins->map(fn ($admin) => [
            'nama' => $admin->nama_karyawan,
            'telp' => $admin->no_telp,
            'cabang' => $admin->kolaborasi?->nama_kolaborasi ?? '',
        ])->values()->all();

        return view('pages.admin-global.kelola-admin', compact('admins', 'branches', 'search', 'adminSearchData'));
    }

    /**
     * Store new admin.
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20|unique:karyawans,no_telp|unique:users,phone',
            'email' => 'nullable|email|max:100|unique:karyawans,email',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'peran' => 'required|in:Admin Kolaborasi,Admin Global',
            'kolaborasi_id' => 'nullable|required_if:peran,Admin Kolaborasi|exists:kolaborasi,id',
            'alamat' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $userRole = match ($request->peran) {
                'Admin Kolaborasi' => UserRole::ADMIN_KOLABORASI,
                'Admin Global' => UserRole::ADMIN_GLOBAL,
            };

            $dob = Carbon::parse($request->tanggal_lahir)->format('d-m-Y');

            $user = User::create([
                'name' => $request->nama_karyawan,
                'phone' => $request->no_telp,
                'password' => Hash::make($dob),
                'role' => $userRole,
            ]);

            Karyawan::create([
                'user_id' => $user->id,
                'nama_karyawan' => $request->nama_karyawan,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                'peran' => $request->peran,
                'kolaborasi_id' => $request->kolaborasi_id,
                'status_karyawan' => 'Aktif',
            ]);
        });

        return redirect()->route('admin-global.kelola-admin')
            ->with('success', 'Admin baru berhasil ditambahkan!');
    }

    /**
     * Update admin details.
     */
    public function updateAdmin(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $user = $karyawan->user;

        $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20|unique:karyawans,no_telp,' . $karyawan->id . '|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|max:100|unique:karyawans,email,' . $karyawan->id,
            'peran' => 'required|in:Admin Kolaborasi,Admin Global',
            'kolaborasi_id' => 'nullable|required_if:peran,Admin Kolaborasi|exists:kolaborasi,id',
            'alamat' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $karyawan, $user) {
            $userRole = match ($request->peran) {
                'Admin Kolaborasi' => UserRole::ADMIN_KOLABORASI,
                'Admin Global' => UserRole::ADMIN_GLOBAL,
            };

            $user->update([
                'name' => $request->nama_karyawan,
                'phone' => $request->no_telp,
                'role' => $userRole,
            ]);

            $karyawan->update([
                'nama_karyawan' => $request->nama_karyawan,
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                'peran' => $request->peran,
                'kolaborasi_id' => $request->peran === 'Admin Global' ? null : $request->kolaborasi_id,
                'alamat' => $request->alamat,
            ]);
        });

        return redirect()->route('admin-global.kelola-admin')
            ->with('success', 'Data admin berhasil diperbarui!');
    }

    /**
     * Toggle admin active status.
     */
    public function toggleStatus($id)
    {
        $karyawan = Karyawan::findOrFail($id);

        if ($karyawan->user_id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri!');
        }

        $newStatus = $karyawan->status_karyawan === 'Aktif' ? 'Tidak Aktif' : 'Aktif';
        $karyawan->update([
            'status_karyawan' => $newStatus
        ]);

        return redirect()->route('admin-global.kelola-admin')
            ->with('success', 'Status admin ' . $karyawan->nama_karyawan . ' berhasil diubah menjadi ' . $newStatus . '!');
    }

    /**
     * Verify identity and reset password to DOB format.
     */
    public function resetPassword(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $user = $karyawan->user;

        $request->validate([
            'nama_karyawan' => 'required|string',
            'no_telp' => 'required|string',
            'peran' => 'required|string',
        ]);

        $normalizeName = fn (?string $name) => mb_strtolower(trim(preg_replace('/\s+/', ' ', $name ?? '')));

        $registeredName = $normalizeName($karyawan->nama_karyawan);
        $submittedName = $normalizeName($request->nama_karyawan);
        $matchName = $registeredName !== '' && $registeredName === $submittedName;

        $matchPhone = trim($karyawan->no_telp) === trim($request->no_telp) || trim($user->phone) === trim($request->no_telp);
        $matchRole = mb_strtolower(trim($karyawan->peran)) === mb_strtolower(trim($request->peran));

        if (!$matchName || !$matchPhone || !$matchRole) {
            return back()->with('error', 'Gagal mereset password: Data verifikasi tidak cocok dengan data terdaftar.');
        }

        if (!$karyawan->tanggal_lahir) {
            return back()->with('error', 'Gagal mereset password: Tanggal lahir admin tidak terdaftar.');
        }

        $dob = Carbon::parse($karyawan->tanggal_lahir)->format('d-m-Y');

        $user->update([
            'password' => Hash::make($dob)
        ]);

        return redirect()->route('admin-global.kelola-admin')
            ->with('success', 'Password admin ' . $karyawan->nama_karyawan . ' berhasil direset ke default (' . $dob . ')!');
    }
}
