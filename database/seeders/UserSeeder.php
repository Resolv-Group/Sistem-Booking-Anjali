<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Karyawan;
use App\Models\Pasien;
use App\Models\TherapistSession;
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
            'kode_karyawan' => 'KRY-' . strtoupper(Str::random(5)),
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
            'kode_karyawan' => 'KRY-' . strtoupper(Str::random(5)),
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
            'kode_karyawan' => 'KRY-' . strtoupper(Str::random(5)),
            'nama_karyawan' => 'Terapis 1',
            'no_telp' => '08333333331',
            'peran' => 'Terapis',
            'tanggal_lahir' => $terapis1TL,
            'jenis_kelamin' => 'L',
            'kolaborasi_id' => 1,
        ]);
        $terapis1->layanans()->attach([1, 2, 3, 4]);

        $user4 = User::create([
            'name' => 'Terapis 2',
            'phone' => '08333333332',
            'password' => Hash::make(Carbon::parse($terapis2TL)->format('d-m-Y')),
            'role' => UserRole::THERAPIST,
        ]);
        $terapis2 = Karyawan::create([
            'user_id' => $user4->id,
            'kode_karyawan' => 'KRY-' . strtoupper(Str::random(5)),
            'nama_karyawan' => 'Terapis 2',
            'no_telp' => '08333333332',
            'peran' => 'Terapis',
            'tanggal_lahir' => $terapis2TL,
            'jenis_kelamin' => 'P',
            'kolaborasi_id' => 2,
        ]);
        $terapis2->layanans()->attach([5, 6, 7, 8, 9]);

        // 2 Pasien
        $user5 = User::create([
            'name' => 'Pasien 1',
            'phone' => '08444444441',
            'password' => Hash::make(Carbon::parse($pasien1TL)->format('d-m-Y')),
            'role' => UserRole::PATIENT,
        ]);
        Pasien::create([
            'user_id' => $user5->id,
            'pasien_public_id' => 'PSN-' . strtoupper(Str::random(8)),
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
            'pasien_public_id' => 'PSN-' . strtoupper(Str::random(8)),
            'nama_pasien' => 'Pasien 2',
            'no_telp' => '08444444442',
            'tanggal_lahir' => $pasien2TL,
            'jenis_kelamin' => 'P',
        ]);

        // Seed Therapist Sessions for Terapis 1 (kolaborasi 1)
        $dates = [Carbon::today(), Carbon::tomorrow(), Carbon::today()->addDays(2)];
        $timesT1 = ['08:00', '10:00', '13:00', '15:00', '18:00'];
        foreach ($dates as $date) {
            foreach ($timesT1 as $index => $time) {
                TherapistSession::create([
                    'terapis_id' => $terapis1->id,
                    'kolaborasi_id' => 1,
                    'tanggal_sesi' => $date->format('Y-m-d'),
                    'waktu_mulai' => $time,
                    'kuota' => [2, 4, 1, 3, 5][$index],
                    'status' => 'terbuka',
                ]);
            }
        }

        // Seed Therapist Sessions for Terapis 2 (kolaborasi 2)
        $timesT2 = ['09:00', '11:00', '14:00', '16:00', '19:00'];
        foreach ($dates as $date) {
            foreach ($timesT2 as $index => $time) {
                TherapistSession::create([
                    'terapis_id' => $terapis2->id,
                    'kolaborasi_id' => 2,
                    'tanggal_sesi' => $date->format('Y-m-d'),
                    'waktu_mulai' => $time,
                    'kuota' => 5,
                    'status' => 'terbuka',
                ]);
            }
        }
    }
}
