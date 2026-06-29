<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingPatient;
use App\Models\Karyawan;
use App\Models\Pasien;
use App\Models\TherapistSchedule;
use App\Models\TherapistSession;
use App\Models\RekamMedis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $adminGlobalUser = User::where('role', \App\Enums\UserRole::ADMIN_GLOBAL)->first();
        $adminKolaborasiUser = User::where('role', \App\Enums\UserRole::ADMIN_KOLABORASI)->first();

        // Load logo_anjali.jpg and encode it in Base64
        $logoPath = public_path('images/logo_anjali.jpg');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        // Fetch patients and therapists by name
        $patients = Pasien::all()->keyBy('nama_pasien');
        $therapists = Karyawan::where('peran', 'Terapis')->get()->keyBy('nama_karyawan');

        $bookingsData = [
            // 4 Completed (Booking 1 is Group)
            [
                'booker_name' => 'David',
                'therapist_name' => 'Andi Wijaya',
                'status' => 'completed',
                'date' => Carbon::today()->subDays(2)->toDateString(),
                'time' => '08:00:00',
                'patients' => [
                    [
                        'patient_name' => 'David',
                        'layanan_id' => 1, // Akupunktur
                        'complaint' => 'Sakit punggung bagian bawah setelah mengangkat barang berat.',
                        'ringkasan' => 'Pasien mengeluhkan sakit punggung. Dilakukan terapi akupunktur medis di area punggung selama 30 menit. Nyeri pasien berkurang secara signifikan.',
                        'goal_terapi' => 'Mengurangi tingkat nyeri punggung dan memulihkan mobilitas pinggang.',
                        'saran_terapi' => 'Hindari mengangkat beban berat (>5kg) selama 3 hari, lakukan peregangan punggung ringan secara mandiri di pagi hari.',
                        'tensi_sys' => '120',
                        'tensi_dia' => '80',
                        'tensi_pulse' => '78',
                        'skala_nyeri' => 3,
                        'tingkat_perbaikan' => 'Membaik signifikan',
                    ],
                    [
                        'patient_name' => 'Shirley',
                        'layanan_id' => 2, // Stimulator
                        'complaint' => 'Leher kaku dan tegang sejak bangun tidur.',
                        'ringkasan' => 'Terapi stimulator pada otot leher berjalan lancar. Otot leher terasa rileks.',
                        'goal_terapi' => 'Meredakan ketegangan otot leher.',
                        'saran_terapi' => 'Kompres hangat leher selama 15 menit sebelum tidur.',
                        'tensi_sys' => '115',
                        'tensi_dia' => '75',
                        'tensi_pulse' => '82',
                        'skala_nyeri' => 2,
                        'tingkat_perbaikan' => 'Hampir sembuh',
                    ]
                ]
            ],
            [
                'booker_name' => 'Cindy',
                'therapist_name' => 'Fajar Hidayat',
                'status' => 'completed',
                'date' => Carbon::today()->subDays(2)->toDateString(),
                'time' => '13:00:00',
                'patients' => [
                    [
                        'patient_name' => 'Cindy',
                        'layanan_id' => 3, // Moksa
                        'complaint' => 'Kaki sering terasa dingin, kesemutan, dan pegal-pegal di malam hari.',
                        'ringkasan' => 'Terapi moksa pada titik akupunktur kaki. Pasien merasakan kehangatan yang menjalar di area kaki, aliran darah terasa lebih lancar.',
                        'goal_terapi' => 'Meningkatkan sirkulasi darah dan menghangatkan tubuh bagian bawah.',
                        'saran_terapi' => 'Gunakan kaos kaki saat tidur, rendam kaki dengan air hangat campur garam sebelum tidur.',
                        'tensi_sys' => '110',
                        'tensi_dia' => '70',
                        'tensi_pulse' => '80',
                        'skala_nyeri' => 2,
                        'tingkat_perbaikan' => 'Membaik',
                    ]
                ]
            ],
            [
                'booker_name' => 'Bambang',
                'therapist_name' => 'Andi Wijaya',
                'status' => 'completed',
                'date' => Carbon::yesterday()->toDateString(),
                'time' => '08:00:00',
                'patients' => [
                    [
                        'patient_name' => 'Bambang',
                        'layanan_id' => 4, // TDP
                        'complaint' => 'Nyeri pada lutut kanan saat ditekuk atau dipakai jongkok.',
                        'ringkasan' => 'Aplikasi terapi TDP infra merah pada lutut kanan. Rasa nyeri berkurang setelah terapi dan sendi lutut terasa lebih hangat.',
                        'goal_terapi' => 'Meredakan inflamasi and mengurangi nyeri pada sendi lutut kanan.',
                        'saran_terapi' => 'Kurangi aktivitas naik-turun tangga sementara waktu, gunakan knee support jika beraktivitas berat.',
                        'tensi_sys' => '130',
                        'tensi_dia' => '85',
                        'tensi_pulse' => '75',
                        'skala_nyeri' => 4,
                        'tingkat_perbaikan' => 'Membaik',
                    ]
                ]
            ],
            [
                'booker_name' => 'Maya',
                'therapist_name' => 'Rina Oktaviani',
                'status' => 'completed',
                'date' => Carbon::yesterday()->toDateString(),
                'time' => '10:00:00',
                'patients' => [
                    [
                        'patient_name' => 'Maya',
                        'layanan_id' => 1, // Akupunktur
                        'complaint' => 'Sakit kepala migrain berulang sejak beberapa hari terakhir.',
                        'ringkasan' => 'Sesi terapi akupunktur kepala berjalan dengan baik. Pasien merasa lebih nyaman dan migrain berkurang.',
                        'goal_terapi' => 'Meredakan migrain kronis.',
                        'saran_terapi' => 'Istirahat yang cukup, hindari stres berlebih, dan kurangi kafein.',
                        'tensi_sys' => '120',
                        'tensi_dia' => '80',
                        'tensi_pulse' => '75',
                        'skala_nyeri' => 3,
                        'tingkat_perbaikan' => 'Membaik',
                    ]
                ]
            ],
            // 2 Approved (scheduled for Today, 1 Group, 1 Personal)
            [
                'booker_name' => 'Bambang',
                'therapist_name' => 'Yoga Prasetyo',
                'status' => 'approved',
                'date' => Carbon::today()->toDateString(),
                'time' => '08:00:00',
                'patients' => [
                    [
                        'patient_name' => 'Bambang',
                        'layanan_id' => 8, // Bekam Medis
                        'complaint' => 'Badan terasa berat, pegal-pegal seluruh tubuh, dan masuk angin.',
                    ],
                    [
                        'patient_name' => 'Siska',
                        'layanan_id' => 9, // Refleksi
                        'complaint' => 'Telapak kaki sakit dan kaku setelah berdiri seharian.',
                    ]
                ]
            ],
            [
                'booker_name' => 'Cindy',
                'therapist_name' => 'Rina Oktaviani',
                'status' => 'approved',
                'date' => Carbon::today()->toDateString(),
                'time' => '13:00:00',
                'patients' => [
                    [
                        'patient_name' => 'Cindy',
                        'layanan_id' => 5, // Cupping
                        'complaint' => 'Pundak dan bahu terasa sangat kaku karena terlalu lama duduk di depan laptop.',
                    ]
                ]
            ],
            // 3 Pending (scheduled for Tomorrow, Booking 8 is Group, do not change)
            [
                'booker_name' => 'Maya',
                'therapist_name' => 'Rina Oktaviani',
                'status' => 'pending',
                'date' => Carbon::tomorrow()->toDateString(),
                'time' => '08:00:00',
                'patients' => [
                    [
                        'patient_name' => 'Maya',
                        'layanan_id' => 1, // Akupunktur
                        'complaint' => 'Mengalami migrain berulang pada kepala sebelah kiri sejak 3 hari terakhir.',
                    ],
                    [
                        'patient_name' => 'David',
                        'layanan_id' => 2, // Stimulator
                        'complaint' => 'Pemulihan pasca cedera engkel ringan akibat terpeleset saat berolahraga.',
                    ]
                ]
            ],
            [
                'booker_name' => 'Shirley',
                'therapist_name' => 'Fajar Hidayat',
                'status' => 'pending',
                'date' => Carbon::tomorrow()->toDateString(),
                'time' => '13:00:00',
                'patients' => [
                    [
                        'patient_name' => 'Shirley',
                        'layanan_id' => 6, // Sleeding Massage
                        'complaint' => 'Pegal-pegal di seluruh tubuh dan rasa lelah setelah perjalanan dinas luar kota.',
                    ]
                ]
            ],
            [
                'booker_name' => 'Cindy',
                'therapist_name' => 'Rina Oktaviani',
                'status' => 'pending',
                'date' => Carbon::tomorrow()->toDateString(),
                'time' => '10:00:00',
                'patients' => [
                    [
                        'patient_name' => 'Cindy',
                        'layanan_id' => 3, // Moksa
                        'complaint' => 'Kaki pegal-pegal rutin setelah beraktivitas.',
                    ]
                ]
            ],
            // 2 Cancelled (scheduled for Today, 1 Group, 1 Personal)
            [
                'booker_name' => 'Kevin',
                'therapist_name' => 'Yoga Prasetyo',
                'status' => 'cancelled',
                'date' => Carbon::today()->toDateString(),
                'time' => '13:00:00',
                'alasan_status' => 'Pasien membatalkan janji temu karena mendadak dipanggil rapat koordinasi di kantor.',
                'patients' => [
                    [
                        'patient_name' => 'Kevin',
                        'layanan_id' => 7, // Pijat Tradisional (Massage)
                        'complaint' => 'Pundak terasa kaku and pegal.',
                    ],
                    [
                        'patient_name' => 'Bambang',
                        'layanan_id' => 8, // Bekam Medis
                        'complaint' => 'Badan terasa kurang fit dan masuk angin.',
                    ]
                ]
            ],
            [
                'booker_name' => 'David',
                'therapist_name' => 'Andi Wijaya',
                'status' => 'cancelled',
                'date' => Carbon::today()->toDateString(),
                'time' => '10:00:00',
                'alasan_status' => 'Pasien mengabari bahwa ban kendaraan bocor dalam perjalanan.',
                'patients' => [
                    [
                        'patient_name' => 'David',
                        'layanan_id' => 9, // Refleksi
                        'complaint' => 'Pegal-pegal di kaki setelah lari pagi.',
                    ]
                ]
            ],
            // 2 Rejected (scheduled for Today, 1 Group, 1 Personal)
            [
                'booker_name' => 'Andi Prasetyo',
                'therapist_name' => 'Rina Oktaviani',
                'status' => 'rejected',
                'date' => Carbon::today()->toDateString(),
                'time' => '10:00:00',
                'alasan_status' => 'Terapis yang bersangkutan harus menghadiri pelatihan eksternal wajib dari pusat pada slot waktu tersebut.',
                'patients' => [
                    [
                        'patient_name' => 'Andi Prasetyo',
                        'layanan_id' => 8, // Bekam Medis
                        'complaint' => 'Melakukan bekam rutin bulanan untuk menjaga kebugaran tubuh.',
                    ],
                    [
                        'patient_name' => 'Siska',
                        'layanan_id' => 9, // Refleksi
                        'complaint' => 'Kaki sering kram saat malam hari.',
                    ]
                ]
            ],
            [
                'booker_name' => 'Shirley',
                'therapist_name' => 'Andi Wijaya',
                'status' => 'rejected',
                'date' => Carbon::today()->toDateString(),
                'time' => '08:00:00',
                'alasan_status' => 'Kuota terapi untuk layanan ini pada jam tersebut sudah penuh.',
                'patients' => [
                    [
                        'patient_name' => 'Shirley',
                        'layanan_id' => 1, // Akupunktur
                        'complaint' => 'Leher pegal dan kaku.',
                    ]
                ]
            ],
        ];

        foreach ($bookingsData as $data) {
            $booker = $patients->get($data['booker_name']);
            $therapist = $therapists->get($data['therapist_name']);

            if (!$booker || !$therapist) {
                continue;
            }

            $carbonDate = Carbon::parse($data['date']);
            $dayOfWeek = $carbonDate->dayOfWeekIso;

            // Find or create schedule for the therapist on this day of week
            $schedule = TherapistSchedule::firstOrCreate([
                'terapis_id' => $therapist->id,
                'hari' => (string)$dayOfWeek,
                'waktu_mulai' => $data['time'],
            ], [
                'kuota' => 5,
                'status' => 'Aktif',
            ]);

            // Create therapist session for this booking slot
            $session = TherapistSession::firstOrCreate([
                'terapis_id' => $therapist->id,
                'tanggal_sesi' => $data['date'],
                'waktu_mulai' => $data['time'],
            ], [
                'operasional_id' => $schedule->id,
                'kolaborasi_id' => $therapist->kolaborasi_id,
                'kuota' => 5,
                'status' => $data['status'] === 'completed' ? 'selesai' : 'terbuka',
            ]);

            // Construct Booking
            $bookingFields = [
                'booking_oleh_pasien_id' => $booker->id,
                'terapis_sesi_id' => $session->id,
                'status' => $data['status'],
                'bukti_transfer_booking_path' => $logoBase64,
                'bukti_transfer_booking_mime' => 'image/jpeg',
                'created_at' => $carbonDate->copy()->setTimeFromTimeString($data['time'])->subHours(2),
                'updated_at' => $carbonDate->copy()->setTimeFromTimeString($data['time'])->subHours(2),
            ];

            if ($data['status'] === 'completed') {
                $bookingFields['approved_by'] = $therapist->user_id;
                $bookingFields['approved_at'] = $carbonDate->copy()->setTimeFromTimeString($data['time'])->subHours(1);
                $bookingFields['completed_by'] = $therapist->user_id;
                $bookingFields['completed_at'] = $carbonDate->copy()->setTimeFromTimeString($data['time'])->addHour();
            } elseif ($data['status'] === 'approved') {
                $bookingFields['approved_by'] = $adminKolaborasiUser ? $adminKolaborasiUser->id : $therapist->user_id;
                $bookingFields['approved_at'] = $carbonDate->copy()->setTimeFromTimeString($data['time'])->subHours(1);
            } elseif ($data['status'] === 'cancelled') {
                $bookingFields['cancelled_by'] = $booker->user_id;
                $bookingFields['cancelled_at'] = $carbonDate->copy()->setTimeFromTimeString($data['time'])->subHours(1);
                $bookingFields['alasan_status'] = $data['alasan_status'];
                $bookingFields['batalkan_type'] = 'Pasien';
            } elseif ($data['status'] === 'rejected') {
                $bookingFields['rejected_by'] = $adminKolaborasiUser ? $adminKolaborasiUser->id : $therapist->user_id;
                $bookingFields['rejected_at'] = $carbonDate->copy()->setTimeFromTimeString($data['time'])->subHours(1);
                $bookingFields['alasan_status'] = $data['alasan_status'];
            }

            $booking = Booking::create($bookingFields);

            // Construct BookingPatients
            foreach ($data['patients'] as $pData) {
                $patientObj = $patients->get($pData['patient_name']);
                if (!$patientObj) {
                    continue;
                }

                $bookingPatientFields = [
                    'booking_id' => $booking->id,
                    'pasien_id' => $patientObj->id,
                    'layanan_id' => $pData['layanan_id'],
                    'keluhan_pasien' => $pData['complaint'],
                    'status_pasien' => $data['status'] === 'completed' ? 'selesai' : ($data['status'] === 'cancelled' || $data['status'] === 'rejected' ? 'dibatalkan' : 'menunggu'),
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                ];

                if ($data['status'] === 'completed') {
                    $bookingPatientFields['started_at'] = $carbonDate->copy()->setTimeFromTimeString($data['time']);
                    $bookingPatientFields['finished_at'] = $carbonDate->copy()->setTimeFromTimeString($data['time'])->addHour();
                    $bookingPatientFields['ringkasan_sesi'] = $pData['ringkasan'];
                }

                $bookingPatient = BookingPatient::create($bookingPatientFields);

                // Create RekamMedis for completed bookings
                if ($data['status'] === 'completed') {
                    RekamMedis::create([
                        'booking_pasien_id' => $bookingPatient->id,
                        'tensi_sys' => $pData['tensi_sys'],
                        'tensi_dia' => $pData['tensi_dia'],
                        'tensi_pulse' => $pData['tensi_pulse'],
                        'goal_terapi' => $pData['goal_terapi'],
                        'saran_rekomendasi' => $pData['saran_terapi'],
                        'catatan_terapis' => $pData['ringkasan'],
                        'skala_nyeri' => $pData['skala_nyeri'],
                        'tingkat_perbaikan' => $pData['tingkat_perbaikan'],
                    ]);
                }
            }
        }
    }
}
