<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Pasien;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminGlobalTL = '1980-01-01';
        $adminCabangTL = '1980-01-01';
        $terapis1TL = '1991-01-01';
        $terapis2TL = '1992-02-02';
        $pasien1TL = '1991-01-01';
        $pasien2TL = '1992-02-02';

        // 1 Admin Global
        User::create([
            'name' => 'Admin Global',
            'phone' => '08111111111',
            'password' => Hash::make(Carbon::parse($adminGlobalTL)->format('d-m-Y')),
            'role' => UserRole::ADMIN_GLOBAL,
        ]);
        Karyawan::create([
            'kode_karyawan' => 'KRY-' . strtoupper(Str::random(5)),
            'nama_karyawan' => 'Admin Global',
            'no_telp' => '08111111111',
            'peran' => 'Admin',
            'tanggal_lahir' => $adminGlobalTL,
            'jenis_kelamin' => 'L',
        ]);

        // 1 Admin RT (Admin Cabang)
        User::create([
            'name' => 'Admin Cabang',
            'phone' => '08222222222',
            'password' => Hash::make(Carbon::parse($adminCabangTL)->format('d-m-Y')),
            'role' => UserRole::ADMIN_CABANG,
        ]);
        Karyawan::create([
            'kode_karyawan' => 'KRY-' . strtoupper(Str::random(5)),
            'nama_karyawan' => 'Admin Cabang',
            'no_telp' => '08222222222',
            'peran' => 'Admin Rumah Terapi',
            'tanggal_lahir' => $adminCabangTL,
            'jenis_kelamin' => 'P',
        ]);

        // 2 Terapis
        User::create([
            'name' => 'Terapis 1',
            'phone' => '08333333331',
            'password' => Hash::make(Carbon::parse($terapis1TL)->format('d-m-Y')),
            'role' => UserRole::THERAPIST,
        ]);
        Karyawan::create([
            'kode_karyawan' => 'KRY-' . strtoupper(Str::random(5)),
            'nama_karyawan' => 'Terapis 1',
            'no_telp' => '08333333331',
            'peran' => 'Terapis',
            'tanggal_lahir' => $terapis1TL,
            'jenis_kelamin' => 'L',
        ]);

        User::create([
            'name' => 'Terapis 2',
            'phone' => '08333333332',
            'password' => Hash::make(Carbon::parse($terapis2TL)->format('d-m-Y')),
            'role' => UserRole::THERAPIST,
        ]);
        Karyawan::create([
            'kode_karyawan' => 'KRY-' . strtoupper(Str::random(5)),
            'nama_karyawan' => 'Terapis 2',
            'no_telp' => '08333333332',
            'peran' => 'Terapis',
            'tanggal_lahir' => $terapis2TL,
            'jenis_kelamin' => 'P',
        ]);

        // 2 Pasien
        User::create([
            'name' => 'Pasien 1',
            'phone' => '08444444441',
            'password' => Hash::make(Carbon::parse($pasien1TL)->format('d-m-Y')),
            'role' => UserRole::PATIENT,
        ]);
        Pasien::create([
            'pasien_public_id' => 'PSN-' . strtoupper(Str::random(8)),
            'nama_pasien' => 'Pasien 1',
            'no_telp' => '08444444441',
            'tanggal_lahir' => $pasien1TL,
            'jenis_kelamin' => 'L',
        ]);

        User::create([
            'name' => 'Pasien 2',
            'phone' => '08444444442',
            'password' => Hash::make(Carbon::parse($pasien2TL)->format('d-m-Y')),
            'role' => UserRole::PATIENT,
        ]);
        Pasien::create([
            'pasien_public_id' => 'PSN-' . strtoupper(Str::random(8)),
            'nama_pasien' => 'Pasien 2',
            'no_telp' => '08444444442',
            'tanggal_lahir' => $pasien2TL,
            'jenis_kelamin' => 'P',
        ]);
    }
}
