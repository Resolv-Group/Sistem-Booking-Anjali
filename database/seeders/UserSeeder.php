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
        $usersData = [
            // Admin Global
            [
                'name' => 'Admin Global',
                'phone' => '081234567001',
                'dob' => '1980-01-01',
                'role' => UserRole::ADMIN_GLOBAL,
                'gender' => 'L',
                'type' => 'karyawan',
                'peran' => 'Admin Global',
                'kolaborasi_id' => 1,
            ],
            // Admin Kolaborasi
            [
                'name' => 'Admin Kolaborasi',
                'phone' => '081234567002',
                'dob' => '1980-01-01',
                'role' => UserRole::ADMIN_KOLABORASI,
                'gender' => 'P',
                'type' => 'karyawan',
                'peran' => 'Admin Kolaborasi',
                'kolaborasi_id' => 1,
            ],
            // Terapis 1
            [
                'name' => 'Andi Wijaya',
                'phone' => '081234567003',
                'dob' => '1991-01-01',
                'role' => UserRole::THERAPIST,
                'gender' => 'L',
                'type' => 'karyawan',
                'peran' => 'Terapis',
                'kolaborasi_id' => 1,
                'layanans' => [1, 2, 3, 4, 8, 9],
            ],
            // Terapis 2
            [
                'name' => 'Rina Oktaviani',
                'phone' => '081234567004',
                'dob' => '1992-02-02',
                'role' => UserRole::THERAPIST,
                'gender' => 'P',
                'type' => 'karyawan',
                'peran' => 'Terapis',
                'kolaborasi_id' => 1,
                'layanans' => [1, 2, 3, 4, 8, 9],
            ],
            // Pasien 1
            [
                'name' => 'David',
                'phone' => '081234567005',
                'dob' => '1991-01-01',
                'role' => UserRole::PATIENT,
                'gender' => 'L',
                'type' => 'pasien',
            ],
            // Pasien 2
            [
                'name' => 'Shirley',
                'phone' => '081234567006',
                'dob' => '1992-02-02',
                'role' => UserRole::PATIENT,
                'gender' => 'P',
                'type' => 'pasien',
            ],
            // Pasien 3
            [
                'name' => 'Maya',
                'phone' => '081234567007',
                'dob' => '1993-03-03',
                'role' => UserRole::PATIENT,
                'gender' => 'P',
                'type' => 'pasien',
            ],
            // Pasien 4
            [
                'name' => 'Bambang',
                'phone' => '081234567008',
                'dob' => '1994-04-04',
                'role' => UserRole::PATIENT,
                'gender' => 'L',
                'type' => 'pasien',
            ],
            // Pasien 5
            [
                'name' => 'Kevin',
                'phone' => '081234567009',
                'dob' => '1995-05-05',
                'role' => UserRole::PATIENT,
                'gender' => 'L',
                'type' => 'pasien',
            ],
            // Pasien 6
            [
                'name' => 'Cindy',
                'phone' => '081234567010',
                'dob' => '1996-06-06',
                'role' => UserRole::PATIENT,
                'gender' => 'P',
                'type' => 'pasien',
            ],
            // Pasien 7
            [
                'name' => 'Siska',
                'phone' => '081234567011',
                'dob' => '1997-07-07',
                'role' => UserRole::PATIENT,
                'gender' => 'P',
                'type' => 'pasien',
            ],
            // Pasien 8
            [
                'name' => 'Andi Prasetyo',
                'phone' => '081234567012',
                'dob' => '1998-08-08',
                'role' => UserRole::PATIENT,
                'gender' => 'L',
                'type' => 'pasien',
            ],
            // Terapis 3
            [
                'name' => 'Yoga Prasetyo',
                'phone' => '081234567013',
                'dob' => '1993-03-03',
                'role' => UserRole::THERAPIST,
                'gender' => 'L',
                'type' => 'karyawan',
                'peran' => 'Terapis',
                'kolaborasi_id' => 1,
                'layanans' => [1, 2, 3, 4, 8, 9],
            ],
            // Terapis 4
            [
                'name' => 'Fajar Hidayat',
                'phone' => '081234567014',
                'dob' => '1994-04-04',
                'role' => UserRole::THERAPIST,
                'gender' => 'L',
                'type' => 'karyawan',
                'peran' => 'Terapis',
                'kolaborasi_id' => 2,
                'layanans' => [3, 4, 5, 6, 7],
            ],
            // Terapis 5
            [
                'name' => 'Putri Lestari',
                'phone' => '081234567015',
                'dob' => '1995-05-05',
                'role' => UserRole::THERAPIST,
                'gender' => 'P',
                'type' => 'karyawan',
                'peran' => 'Terapis',
                'kolaborasi_id' => 2,
                'layanans' => [3, 4, 5, 6, 7],
            ],
            // Terapis 6
            [
                'name' => 'Arif Nugroho',
                'phone' => '081234567016',
                'dob' => '1996-06-06',
                'role' => UserRole::THERAPIST,
                'gender' => 'L',
                'type' => 'karyawan',
                'peran' => 'Terapis',
                'kolaborasi_id' => 2,
                'layanans' => [3, 4, 5, 6, 7],
            ],
        ];

        foreach ($usersData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'password' => Hash::make(Carbon::parse($data['dob'])->format('d-m-Y')),
                'role' => $data['role'],
            ]);

            if ($data['type'] === 'karyawan') {
                $karyawan = Karyawan::create([
                    'user_id' => $user->id,
                    'nama_karyawan' => $data['name'],
                    'no_telp' => $data['phone'],
                    'peran' => $data['peran'],
                    'tanggal_lahir' => $data['dob'],
                    'jenis_kelamin' => $data['gender'],
                    'kolaborasi_id' => $data['kolaborasi_id'],
                ]);
                if (isset($data['layanans'])) {
                    $karyawan->layanans()->attach($data['layanans']);
                }

                // If role is therapist, seed operating schedules for Mon-Sat
                if ($data['role'] === UserRole::THERAPIST) {
                    for ($hari = 1; $hari <= 6; $hari++) {
                        TherapistSchedule::create([
                            'terapis_id' => $karyawan->id,
                            'hari' => (string)$hari,
                            'waktu_mulai' => '08:00:00',
                            'kuota' => 5,
                            'status' => 'Aktif',
                        ]);
                        TherapistSchedule::create([
                            'terapis_id' => $karyawan->id,
                            'hari' => (string)$hari,
                            'waktu_mulai' => '10:00:00',
                            'kuota' => 5,
                            'status' => 'Aktif',
                        ]);
                        TherapistSchedule::create([
                            'terapis_id' => $karyawan->id,
                            'hari' => (string)$hari,
                            'waktu_mulai' => '13:00:00',
                            'kuota' => 5,
                            'status' => 'Aktif',
                        ]);
                    }
                }
            } else {
                Pasien::create([
                    'user_id' => $user->id,
                    'nama_pasien' => $data['name'],
                    'no_telp' => $data['phone'],
                    'tanggal_lahir' => $data['dob'],
                    'jenis_kelamin' => $data['gender'],
                    'kode_referral' => Pasien::generateUniqueReferralCode(),
                ]);
            }
        }
    }
}
