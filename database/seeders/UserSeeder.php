<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Karyawan;
use App\Models\Pasien;
use App\Models\TherapistSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminGlobalTL = '1980-01-01';
        $adminKolaborasiTL = '1980-01-01';
        $terapis1TL = '1991-01-01';
        $terapis2TL = '1992-02-02';
        $pasien1TL = '1991-01-01';
        $pasien2TL = '1992-02-02';

        // 1 Admin Global
        $user1 = User::create([
            'name' => 'Admin Global',
            'phone' => '08111111111',
            'password' => Hash::make(Carbon::parse($adminGlobalTL)->format('d-m-Y')),
            'role' => UserRole::ADMIN_GLOBAL,
        ]);
        Karyawan::create([
            'user_id' => $user1->id,
            'nama_karyawan' => 'Admin Global',
            'no_telp' => '08111111111',
            'peran' => 'Admin Global',
            'tanggal_lahir' => $adminGlobalTL,
            'jenis_kelamin' => 'L',
            'kolaborasi_id' => 1,
        ]);

        // 1 Admin Kolaborasi
        $user2 = User::create([
            'name' => 'Admin Kolaborasi',
            'phone' => '08222222222',
            'password' => Hash::make(Carbon::parse($adminKolaborasiTL)->format('d-m-Y')),
            'role' => UserRole::ADMIN_KOLABORASI,
        ]);
        Karyawan::create([
            'user_id' => $user2->id,
            'nama_karyawan' => 'Admin Kolaborasi',
            'no_telp' => '08222222222',
            'peran' => 'Admin Kolaborasi',
            'tanggal_lahir' => $adminKolaborasiTL,
            'jenis_kelamin' => 'P',
            'kolaborasi_id' => 1,
        ]);

        // 2 Terapis
        $user3 = User::create([
            'name' => 'Terapis 1',
            'phone' => '08333333331',
            'password' => Hash::make(Carbon::parse($terapis1TL)->format('d-m-Y')),
            'role' => UserRole::THERAPIST,
        ]);
        $terapis1 = Karyawan::create([
            'user_id' => $user3->id,
            'nama_karyawan' => 'Terapis 1',
            'no_telp' => '08333333331',
            'peran' => 'Terapis',
            'tanggal_lahir' => $terapis1TL,
            'jenis_kelamin' => 'L',
            'kolaborasi_id' => 1,
        ]);
        $terapis1->layanans()->attach([1, 2, 3, 4]);

        // Seed Therapist Schedule untuk Terapis 1
        for ($day = 1; $day <= 7; $day++) {
            TherapistSchedule::create([

                'terapis_id' => $user3->id,

                'hari' => $day,

                'waktu_mulai' => '08:00:00',

                'kuota' => 10,

                'status' => 'Tidak Aktif',
            ]);
        }

        $user4 = User::create([
            'name' => 'Terapis 2',
            'phone' => '08333333332',
            'password' => Hash::make(Carbon::parse($terapis2TL)->format('d-m-Y')),
            'role' => UserRole::THERAPIST,
        ]);
        $terapis2 = Karyawan::create([
            'user_id' => $user4->id,
            'nama_karyawan' => 'Terapis 2',
            'no_telp' => '08333333332',
            'peran' => 'Terapis',
            'tanggal_lahir' => $terapis2TL,
            'jenis_kelamin' => 'P',
            'kolaborasi_id' => 2,
        ]);
        $terapis2->layanans()->attach([5, 6, 7, 8, 9]);

        // Seed Therapist Schedule untuk Terapis 2
        for ($day = 1; $day <= 7; $day++) {
            TherapistSchedule::create([

                'terapis_id' => $user4->id,

                'hari' => $day,

                'waktu_mulai' => '08:00:00',

                'kuota' => 10,

                'status' => 'Tidak Aktif',
            ]);
        }

        // 2 Pasien
        $user5 = User::create([
            'name' => 'Pasien 1',
            'phone' => '08444444441',
            'password' => Hash::make(Carbon::parse($pasien1TL)->format('d-m-Y')),
            'role' => UserRole::PATIENT,
        ]);
        Pasien::create([
            'user_id' => $user5->id,
            'nama_pasien' => 'Pasien 1',
            'no_telp' => '08444444441',
            'tanggal_lahir' => $pasien1TL,
            'jenis_kelamin' => 'L',
        ]);

        $user6 = User::create([
            'name' => 'Pasien 2',
            'phone' => '08444444442',
            'password' => Hash::make(Carbon::parse($pasien2TL)->format('d-m-Y')),
            'role' => UserRole::PATIENT,
        ]);
        Pasien::create([
            'user_id' => $user6->id,
            'nama_pasien' => 'Pasien 2',
            'no_telp' => '08444444442',
            'tanggal_lahir' => $pasien2TL,
            'jenis_kelamin' => 'P',
        ]);

    }
}
