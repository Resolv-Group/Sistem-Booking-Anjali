<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $terapis = Karyawan::where('peran', 'Terapis')
            ->where('status_karyawan', 'Aktif')
            ->with(['cabang', 'layanans'])
            ->get();

        return view('pages.booking.patient.index', compact('terapis'));
    }

    public function create(Request $request)
    {
        $therapistId = $request->query('therapist_id');
        
        $therapist = null;
        if ($therapistId) {
            $therapist = Karyawan::with(['cabang', 'layanans'])->find($therapistId);
        }
        
        if (!$therapist) {
            $therapist = Karyawan::where('peran', 'Terapis')
                ->where('status_karyawan', 'Aktif')
                ->with(['cabang', 'layanans'])
                ->firstOrFail();
        }

        // Map layanans for Alpine.js
        $services = $therapist->layanans->map(function ($layanan) {
            return [
                'id' => $layanan->id,
                'name' => $layanan->nama,
                'price' => (int) $layanan->base_harga,
                'desc' => $layanan->deskripsi ?: 'Layanan profesional terapis.'
            ];
        });

        // Get available sessions
        $sessions = \App\Models\TherapistSession::where('terapis_id', $therapist->id)
            ->where('status', 'terbuka')
            ->where('tanggal_sesi', '>=', now()->toDateString())
            ->get()
            ->map(function ($sesi) {
                return [
                    'id' => $sesi->id,
                    'tanggal_sesi' => $sesi->tanggal_sesi,
                    'waktu_mulai' => substr($sesi->waktu_mulai, 0, 5),
                    'kuota_sisa' => (int) $sesi->remaining_capacity,
                    'used_capacity' => (int) $sesi->used_capacity,
                ];
            });

        return view('pages.booking.patient.form', compact('therapist', 'services', 'sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'terapis_sesi_id' => 'required|exists:terapis_sesi,id',
            'services' => 'required',
            'slots' => 'required|integer|min:1|max:3',
            'payment_proof' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $primaryUser = auth()->user() ?: \App\Models\User::where('role', \App\Enums\UserRole::PATIENT)->first();
        if (!$primaryUser) {
            return redirect()->back()->with('error', 'Silakan login terlebih dahulu.');
        }
        $primaryPasien = $primaryUser->pasien;

        // Parse services
        $serviceIds = json_decode($request->services, true);
        if (empty($serviceIds)) {
            return redirect()->back()->with('error', 'Silakan pilih minimal satu layanan.');
        }

        // Upload payment proof
        $path = null;
        $mime = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $path = $file->store('bukti_transfer', 'public');
            $mime = $file->getClientMimeType();
        }

        // Create the primary Booking
        $booking = \App\Models\Booking::create([
            'booking_oleh_pasien_id' => $primaryPasien->id,
            'terapis_sesi_id' => $request->terapis_sesi_id,
            'layanan_id' => $serviceIds[0],
            'status' => 'pending',
            'bukti_transfer_booking_path' => $path,
            'bukti_transfer_booking_mime' => $mime,
        ]);

        $slots = (int) $request->slots;
        if ($slots === 1) {
            // Individual Mode
            \App\Models\BookingPatient::create([
                'booking_id' => $booking->id,
                'pasien_id' => $primaryPasien->id,
                'keluhan_pasien' => $request->complaint_main,
                'status_pasien' => 'menunggu',
            ]);
        } else {
            // Group Mode
            $names = $request->input('patient_names', []);
            $complaints = $request->input('patient_complaints', []);

            for ($i = 0; $i < $slots; $i++) {
                if ($i === 0) {
                    // Patient 1 (Primary)
                    \App\Models\BookingPatient::create([
                        'booking_id' => $booking->id,
                        'pasien_id' => $primaryPasien->id,
                        'keluhan_pasien' => $complaints[0] ?? null,
                        'status_pasien' => 'menunggu',
                    ]);
                } else {
                    // Patient 2, 3 (Secondary)
                    $extraName = $names[$i] ?? ('Pasien Tambahan ' . ($i + 1));
                    $extraComplaint = $complaints[$i] ?? null;

                    // Register extra guest patient
                    $extraUser = \App\Models\User::create([
                        'name' => $extraName,
                        'phone' => $primaryUser->phone . '-' . ($i + 1) . '-' . \Illuminate\Support\Str::random(3),
                        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                        'role' => \App\Enums\UserRole::PATIENT,
                    ]);

                    $extraPasien = \App\Models\Pasien::create([
                        'user_id' => $extraUser->id,
                        'pasien_public_id' => 'PSN-' . strtoupper(\Illuminate\Support\Str::random(8)),
                        'nama_pasien' => $extraName,
                        'no_telp' => $extraUser->phone,
                    ]);

                    \App\Models\BookingPatient::create([
                        'booking_id' => $booking->id,
                        'pasien_id' => $extraPasien->id,
                        'keluhan_pasien' => $extraComplaint,
                        'status_pasien' => 'menunggu',  
                    ]);
                }
            }
        }

        return redirect()->route('patient.booking.form-selesai')->with('success', 'Booking berhasil diajukan! Menunggu verifikasi.');
    }
}
