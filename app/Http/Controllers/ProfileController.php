<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        // if ($request->user()->isDirty('phone')) {
        //     $request->user()->phone_verified_at = null;
        // }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function editProfileAdminGlobal()
    {
        $admin_global = auth()->user()->karyawan;

        return view('pages.profile.data-pribadi.admin-global-dp', compact('admin_global'));
    }

    public function updateProfileAdminGlobal(Request $request)
    {
        $admin_global = auth()->user()->karyawan;
        $user = auth()->user();

        $validated = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'nik' => ['nullable', 'digits:16', Rule::unique('karyawans', 'nik')->ignore($admin_global->id)],
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'email' => ['nullable', 'email', Rule::unique('karyawans', 'email')->ignore($admin_global->id)],
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
            'no_telp' => ['required', 'string', Rule::unique('karyawans', 'no_telp')->ignore($admin_global->id)],
        ]);

        // Siapkan array kosong untuk menampung perubahan data User
        $userUpdates = [];

        // 2. Satukan Logika Update untuk tabel Pasien
        // Karena 'updated_by' selalu berubah jika ada update profil, masukkan langsung di sini
        $admin_globalUpdates = array_merge($validated, [
            'updated_by' => $admin_global->user_id,
        ]);

        // 3. Handle File Foto untuk BYTEA Postgres
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $admin_globalUpdates['foto'] = base64_encode(file_get_contents($file->getRealPath()));
            $admin_globalUpdates['foto_mime'] = $file->getClientMimeType();
        } else {
            // Hapus key 'foto' dari array jika tidak ada file baru yang diunggah
            unset($admin_globalUpdates['foto']);
        }

        // 4. Sinkronisasi Data ke Tabel Users jika ada perubahan
        if ($request->filled('nama_karyawan')) {
            $userUpdates['name'] = $request->nama_karyawan;
        }

        if ($request->filled('no_telp')) {
            $userUpdates['phone'] = $request->no_telp;
        }

        if ($request->filled('password')) {
            $userUpdates['password'] = Hash::make($request->password);
        }

        // 5. Eksekusi Query ke Database (Hanya 1x Update per tabel!)
        if (! empty($userUpdates)) {
            $user->update($userUpdates); // 'updated_at' otomatis diurus oleh Laravel
        }

        $admin_global->update($admin_globalUpdates); // 'updated_at' otomatis diurus oleh Laravel

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    public function editProfileAdminCabang()
    {
        $admin_kolaborasi = auth()->user()->karyawan;

        return view('pages.profile.data-pribadi.admin-cabang-dp', compact('admin_kolaborasi'));
    }

    public function updateProfileAdminCabang(Request $request)
    {
        $admin_kolaborasi = auth()->user()->karyawan;
        $user = auth()->user();

        $validated = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'nik' => ['nullable', 'digits:16', Rule::unique('karyawans', 'nik')->ignore($admin_kolaborasi->id)],
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'email' => ['nullable', 'email', Rule::unique('karyawans', 'email')->ignore($admin_kolaborasi->id)],
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
            'no_telp' => ['required', 'string', Rule::unique('karyawans', 'no_telp')->ignore($admin_kolaborasi->id)],
        ]);

        // Siapkan array kosong untuk menampung perubahan data User
        $userUpdates = [];

        // 2. Satukan Logika Update untuk tabel Pasien
        // Karena 'updated_by' selalu berubah jika ada update profil, masukkan langsung di sini
        $admin_kolaborasiUpdates = array_merge($validated, [
            'updated_by' => $admin_kolaborasi->user_id,
        ]);

        // 3. Handle File Foto untuk BYTEA Postgres
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $admin_kolaborasiUpdates['foto'] = base64_encode(file_get_contents($file->getRealPath()));
            $admin_kolaborasiUpdates['foto_mime'] = $file->getClientMimeType();
        } else {
            // Hapus key 'foto' dari array jika tidak ada file baru yang diunggah
            unset($admin_kolaborasiUpdates['foto']);
        }

        // 4. Sinkronisasi Data ke Tabel Users jika ada perubahan
        if ($request->filled('nama_karyawan')) {
            $userUpdates['name'] = $request->nama_karyawan;
        }

        if ($request->filled('no_telp')) {
            $userUpdates['phone'] = $request->no_telp;
        }

        if ($request->filled('password')) {
            $userUpdates['password'] = Hash::make($request->password);
        }

        // 5. Eksekusi Query ke Database (Hanya 1x Update per tabel!)
        if (! empty($userUpdates)) {
            $user->update($userUpdates); // 'updated_at' otomatis diurus oleh Laravel
        }

        $admin_kolaborasi->update($admin_kolaborasiUpdates); // 'updated_at' otomatis diurus oleh Laravel

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    public function editProfileTherapist()
    {
        $therapist = auth()->user()->karyawan;

        return view('pages.profile.data-pribadi.therapist-dp', compact('therapist'));
    }

    public function updateProfileTherapist(Request $request)
    {
        $therapist = auth()->user()->karyawan;
        $user = auth()->user();

        $validated = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'nik' => ['nullable', 'digits:16', Rule::unique('karyawans', 'nik')->ignore($therapist->id)],
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'email' => ['nullable', 'email', Rule::unique('karyawans', 'email')->ignore($therapist->id)],
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
            'no_telp' => ['required', 'string', Rule::unique('karyawans', 'no_telp')->ignore($therapist->id)],
        ]);

        // Siapkan array kosong untuk menampung perubahan data User
        $userUpdates = [];

        // 2. Satukan Logika Update untuk tabel Pasien
        // Karena 'updated_by' selalu berubah jika ada update profil, masukkan langsung di sini
        $therapistUpdates = array_merge($validated, [
            'updated_by' => $therapist->user_id,
        ]);

        // 3. Handle File Foto untuk BYTEA Postgres
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $therapistUpdates['foto'] = base64_encode(file_get_contents($file->getRealPath()));
            $therapistUpdates['foto_mime'] = $file->getClientMimeType();
        } else {
            // Hapus key 'foto' dari array jika tidak ada file baru yang diunggah
            unset($therapistUpdates['foto']);
        }

        // 4. Sinkronisasi Data ke Tabel Users jika ada perubahan
        if ($request->filled('nama_karyawan')) {
            $userUpdates['name'] = $request->nama_karyawan;
        }

        if ($request->filled('no_telp')) {
            $userUpdates['phone'] = $request->no_telp;
        }

        if ($request->filled('password')) {
            $userUpdates['password'] = Hash::make($request->password);
        }

        // 5. Eksekusi Query ke Database (Hanya 1x Update per tabel!)
        if (! empty($userUpdates)) {
            $user->update($userUpdates); // 'updated_at' otomatis diurus oleh Laravel
        }

        $therapist->update($therapistUpdates); // 'updated_at' otomatis diurus oleh Laravel

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    public function editProfilePasien()
    {
        // Mengasumsikan user yang login memiliki relasi ke pasien
        $pasien = auth()->user()->pasien;

        return view('pages.profile.data-pribadi.patient-dp', compact('pasien'));
    }

    public function updateProfilePasien(Request $request)
    {
        $pasien = auth()->user()->pasien;
        $user = auth()->user();

        // 1. Validasi Data
        $validated = $request->validate([
            'nama_pasien' => 'required|string|max:255',
            'nik' => ['nullable', 'digits:16', Rule::unique('pasiens', 'nik')->ignore($pasien->id)],
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'email' => ['nullable', 'email', Rule::unique('pasiens', 'email')->ignore($pasien->id)],
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
            'no_telp' => ['required', 'string', Rule::unique('pasiens', 'no_telp')->ignore($pasien->id)],
        ]);

        // Siapkan array kosong untuk menampung perubahan data User
        $userUpdates = [];

        // 2. Satukan Logika Update untuk tabel Pasien
        // Karena 'updated_by' selalu berubah jika ada update profil, masukkan langsung di sini
        $pasienUpdates = array_merge($validated, [
            'updated_by' => $pasien->user_id,
        ]);

        // 3. Handle File Foto untuk BYTEA Postgres
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $pasienUpdates['foto'] = base64_encode(file_get_contents($file->getRealPath()));
            $pasienUpdates['foto_mime'] = $file->getClientMimeType();
        } else {
            // Hapus key 'foto' dari array jika tidak ada file baru yang diunggah
            unset($pasienUpdates['foto']);
        }

        // 4. Sinkronisasi Data ke Tabel Users jika ada perubahan
        if ($request->filled('nama_pasien')) {
            $userUpdates['name'] = $request->nama_pasien;
        }

        if ($request->filled('no_telp')) {
            $userUpdates['phone'] = $request->no_telp;
        }

        if ($request->filled('password')) {
            $userUpdates['password'] = Hash::make($request->password);
        }

        // 5. Eksekusi Query ke Database (Hanya 1x Update per tabel!)
        if (! empty($userUpdates)) {
            $user->update($userUpdates); // 'updated_at' otomatis diurus oleh Laravel
        }

        $pasien->update($pasienUpdates); // 'updated_at' otomatis diurus oleh Laravel

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}
