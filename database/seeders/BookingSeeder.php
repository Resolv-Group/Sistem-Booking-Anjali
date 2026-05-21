<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingPatient;
use App\Models\Karyawan;
use App\Models\Pasien;
use App\Models\TherapistSchedule;
use App\Models\TherapistSession;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing therapist, schedules, pasiens
        $therapist = Karyawan::where('peran', 'Terapis')->first();
        if (! $therapist) {
            return;
        }

        $schedule = TherapistSchedule::where('terapis_id', $therapist->id)->first();
        if (! $schedule) {
            return;
        }

        $pasien1 = Pasien::first();
        $pasien2 = Pasien::skip(1)->first();

        if (! $pasien1 || ! $pasien2) {
            return;
        }

        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        // 1. Create a Therapist Session for today
        $sessionToday = TherapistSession::create([
            'terapis_id' => $therapist->id,
            'operasional_id' => $schedule->id,
            'kolaborasi_id' => $therapist->kolaborasi_id,
            'tanggal_sesi' => $today,
            'waktu_mulai' => '08:00:00',
            'kuota' => 5,
            'status' => 'terbuka',
        ]);

        // 2. Create a Therapist Session for tomorrow
        $sessionTomorrow = TherapistSession::create([
            'terapis_id' => $therapist->id,
            'operasional_id' => $schedule->id,
            'kolaborasi_id' => $therapist->kolaborasi_id,
            'tanggal_sesi' => $tomorrow,
            'waktu_mulai' => '10:00:00',
            'kuota' => 5,
            'status' => 'terbuka',
        ]);

        // 3. Create a Pending Personal Booking for today (with payment proof)
        $booking1 = Booking::create([
            'booking_oleh_pasien_id' => $pasien1->id,
            'terapis_sesi_id' => $sessionToday->id,
            'status' => 'pending',
            'bukti_transfer_booking_path' => 'bukti_transfer/sample_proof.jpg',
            'bukti_transfer_booking_mime' => 'image/jpeg',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_by' => null,
            'approved_at' => null,
            'rejected_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
            'approved_by' => null,
            'rejected_by' => null,
            'completed_by' => null,
            'cancelled_by' => null,
            'alasan_status' => null,
        ]);

        BookingPatient::create([
            'booking_id' => $booking1->id,
            'pasien_id' => $pasien1->id,
            'layanan_id' => 1,
            'keluhan_pasien' => 'Sakit punggung bagian bawah',
            'status_pasien' => 'menunggu',
        ]);

        // 4. Create a Pending Group Booking for tomorrow (with 2 patients)
        $booking2 = Booking::create([
            'booking_oleh_pasien_id' => $pasien2->id,
            'terapis_sesi_id' => $sessionTomorrow->id,
            'status' => 'pending',
            'bukti_transfer_booking_path' => 'bukti_transfer/sample_proof.jpg',
            'bukti_transfer_booking_mime' => 'image/jpeg',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_by' => null,
            'approved_at' => null,
            'rejected_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
            'approved_by' => null,
            'rejected_by' => null,
            'completed_by' => null,
            'cancelled_by' => null,
            'alasan_status' => null,
        ]);

        // Patient 1 in Group
        BookingPatient::create([
            'booking_id' => $booking2->id,
            'pasien_id' => $pasien2->id,
            'layanan_id' => 2,
            'keluhan_pasien' => 'Pundak kaku dan tegang',
            'status_pasien' => 'menunggu',
        ]);

        // Patient 2 in Group (Pasien 1 is guest member here)
        BookingPatient::create([
            'booking_id' => $booking2->id,
            'pasien_id' => $pasien1->id,
            'layanan_id' => 3,
            'keluhan_pasien' => 'Sakit leher',
            'status_pasien' => 'menunggu',
        ]);

        // 5. Create an Approved Booking for today (to test status state representation)
        $booking3 = Booking::create([
            'booking_oleh_pasien_id' => $pasien1->id,
            'terapis_sesi_id' => $sessionToday->id,
            'status' => 'approved',
            'bukti_transfer_booking_path' => 'bukti_transfer/sample_proof.jpg',
            'bukti_transfer_booking_mime' => 'image/jpeg',
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_by' => null,
            'approved_at' => Carbon::now(),
            'rejected_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
            'approved_by' => $therapist->id,
            'rejected_by' => null,
            'completed_by' => null,
            'cancelled_by' => null,
            'alasan_status' => null,
        ]);

        BookingPatient::create([
            'booking_id' => $booking3->id,
            'pasien_id' => $pasien1->id,
            'layanan_id' => 4,
            'keluhan_pasien' => 'Terapi rutin pasca cedera kaki',
            'status_pasien' => 'menunggu',
        ]);
    }
}
