<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingPatient;
use App\Models\Karyawan;
use App\Models\Kolaborasi;
use App\Models\Pasien;
use App\Models\TherapistSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $allKolaborasis = Kolaborasi::all();

        $terapis = Karyawan::where('peran', 'Terapis')
            ->where('status_karyawan', 'Aktif')
            ->with([
                'kolaborasi',
                'layanans',
                'sessions' => function ($q) {
                    $q->where('tanggal_sesi', '>=', now()->toDateString())
                        ->where('status', 'terbuka') // Match your screenshot 'terbuka'
                        ->with('bookings.bookingPatients');
                },
            ])
            ->get();

        $uniqueCities = Kolaborasi::pluck('kota_kolaborasi')->unique()->sort();

        return view('pages.booking.patient.index', compact('terapis', 'allKolaborasis', 'uniqueCities'));
    }

    public function adminBookingListIndex(Request $request)
    {
        $user = auth()->user();
        if (! $user || ! $user->karyawan) {
            abort(403, 'Akses ditolak. Anda bukan admin cabang.');
        }

        $kolaborasiId = $user->karyawan->kolaborasi_id;

        // Fetch bookings for the branch
        $bookings = Booking::whereHas('session', function ($query) use ($kolaborasiId) {
            $query->where('kolaborasi_id', $kolaborasiId);
        })
            ->with(['patient', 'bookingPatients.pasien', 'bookingPatients.layanan', 'session.therapist'])
            ->orderBy('id', 'asc')
            ->get();

        // Map bookings for Alpine.js
        $mappedBookings = $bookings->map(function ($booking) {
            $patients = $booking->bookingPatients;
            $primaryPatient = $patients->first();
            $extraPatients = $patients->slice(1);

            return [
                'id_raw' => $booking->id,
                'id' => 'BK-'.str_pad($booking->id, 5, '0', STR_PAD_LEFT),
                'nama' => $booking->patient->nama_pasien ?? 'Unknown',
                'status' => $booking->bukti_transfer_booking_path ? 'paid' : 'unpaid',
                'booking_status' => $booking->status, // pending, approved, rejected, completed, cancelled
                'tipe' => $patients->count() > 1 ? 'Group' : 'Personal',
                'extra' => max(0, $patients->count() - 1),
                'peserta' => $extraPatients->map(function ($bp) {
                    return $bp->pasien->nama_pasien ?? 'Pasien Tambahan';
                })->values()->all(),
                'showPeserta' => false,
                'terapis' => $booking->session->therapist->nama_karyawan ?? 'Unknown',
                'waktu' => Carbon::parse($booking->session->tanggal_sesi)->translatedFormat('d F Y').' • '.substr($booking->session->waktu_mulai, 0, 5),
                'bukti_transfer_url' => $booking->bukti_transfer_booking_path ? asset('storage/'.$booking->bukti_transfer_booking_path) : null,
                'alasan_status' => $booking->alasan_status,
                'batalkan_type' => $booking->batalkan_type,
            ];
        });

        // Calculate dynamic stats
        $pendingCount = Booking::where('status', 'pending')
            ->whereHas('session', function ($query) use ($kolaborasiId) {
                $query->where('kolaborasi_id', $kolaborasiId);
            })->count();

        $totalTodayCount = BookingPatient::whereHas('booking', function ($q) use ($kolaborasiId) {
            $q->whereIn('status', ['pending', 'approved', 'completed'])
                ->whereHas('session', function ($q2) use ($kolaborasiId) {
                    $q2->where('kolaborasi_id', $kolaborasiId)
                        ->whereDate('tanggal_sesi', now()->toDateString());
                });
        })->count();

        return view('pages.booking.admin.index', compact('mappedBookings', 'pendingCount', 'totalTodayCount'));
    }

    public function accept(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Booking ini sudah tidak berstatus pending.');
        }

        $user = auth()->user();
        if (! $user || ! $user->karyawan) {
            abort(403);
        }

        $adminKolaborasiId = $user->karyawan->kolaborasi_id;
        $bookingKolaborasiId = $booking->session->kolaborasi_id;

        if ($adminKolaborasiId !== $bookingKolaborasiId) {
            abort(403, 'Akses ditolak. Booking ini bukan milik cabang Anda.');
        }

        $session = $booking->session;
        if ($session->remaining_capacity < 0) {
            return redirect()->back()->with('error', 'Kapasitas kuota sesi terapis tidak mencukupi untuk menerima janji ini.');
        }

        $booking->update([
            'status' => 'approved',
            'updated_at' => now(),
            'updated_by' => $user->id,
            'approved_at' => now(),
            'approved_by' => $user->id,
        ]);

        $booking->bookingPatients()->update([
            'status_pasien' => 'dibatalkan',
        ]);

        return redirect()->back()->with('success', 'Janji temu berhasil disetujui.');
    }

    public function reject(Booking $booking, Request $request)
    {
        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Booking ini sudah tidak berstatus pending.');
        }

        $user = auth()->user();
        if (! $user || ! $user->karyawan) {
            abort(403);
        }

        $adminKolaborasiId = $user->karyawan->kolaborasi_id;
        $bookingKolaborasiId = $booking->session->kolaborasi_id;

        if ($adminKolaborasiId !== $bookingKolaborasiId) {
            abort(403, 'Akses ditolak. Booking ini bukan milik cabang Anda.');
        }

        $request->validate([
            'alasan_status' => 'required|string|max:500',
        ]);

        $booking->update([
            'status' => 'rejected',
            'alasan_status' => $request->alasan_status,
            'updated_at' => now(),
            'updated_by' => $user->id,
            'rejected_at' => now(),
            'rejected_by' => $user->id,
        ]);

        $booking->bookingPatients()->update([
            'status_pasien' => 'dibatalkan',
        ]);

        return redirect()->back()->with('success', 'Janji temu berhasil ditolak.');
    }

    public function cancelApproval(Booking $booking, Request $request)
    {
        if ($booking->status !== 'approved') {
            return redirect()->back()->with('error', 'Booking ini tidak berstatus disetujui.');
        }

        $user = auth()->user();
        if (! $user || ! $user->karyawan) {
            abort(403);
        }

        $adminKolaborasiId = $user->karyawan->kolaborasi_id;
        $bookingKolaborasiId = $booking->session->kolaborasi_id;

        if ($adminKolaborasiId !== $bookingKolaborasiId) {
            abort(403, 'Akses ditolak. Booking ini bukan milik cabang Anda.');
        }

        $request->validate([
            'alasan_status' => 'required|string|max:500',
            'batalkan_type' => 'nullable|string|max:100',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'alasan_status' => $request->alasan_status,
            'batalkan_type' => $request->batalkan_type,
            'updated_at' => now(),
            'updated_by' => $user->id,
            'cancelled_at' => now(),
            'cancelled_by' => $user->id,
        ]);

        $booking->bookingPatients()->update([
            'status_pasien' => 'dibatalkan',
        ]);

        return redirect()->back()->with('success', 'Janji temu berhasil dibatalkan.');
    }

    public function create(Request $request)
    {
        $therapistId = $request->query('therapist_id');

        $therapist = null;
        if ($therapistId) {
            $therapist = Karyawan::with(['kolaborasi', 'layanans'])->find($therapistId);
        }

        if (! $therapist) {
            $therapist = Karyawan::where('peran', 'Terapis')
                ->where('status_karyawan', 'Aktif')
                ->with(['kolaborasi', 'layanans'])
                ->firstOrFail();
        }

        // Map layanans for Alpine.js
        $services = $therapist->layanans->map(function ($layanan) {
            return [
                'id' => $layanan->id,
                'name' => $layanan->nama,
                'price' => (int) $layanan->base_harga,
                'homecare_price' => (int) $layanan->homecare_harga,
                'discount' => (float) $layanan->diskon_persentase,
                'desc' => $layanan->deskripsi ?: 'Layanan profesional terapis.',
            ];
        });

        // Get available sessions
        $sessions = TherapistSession::where('terapis_id', $therapist->id)
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
        // dd($request->all());
        $request->validate([
            'terapis_sesi_id' => 'required|exists:terapis_sesi,id',
            'services' => 'nullable',
            'slots' => 'required|integer|min:1|max:3',
            'payment_proof' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $primaryUser = auth()->user() ?: User::where('role', UserRole::PATIENT)->first();
        if (! $primaryUser) {
            return redirect()->back()->with('error', 'Silakan login terlebih dahulu.');
        }
        $primaryPasien = $primaryUser->pasien;

        // Parse services
        $serviceIds = json_decode($request->services, true) ?? [];

        // Upload payment proof
        $path = null;
        $mime = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $path = $file->store('bukti_transfer', 'public');
            $mime = $file->getClientMimeType();
        }

        // Create the primary Booking
        $booking = Booking::create([
            'booking_oleh_pasien_id' => $primaryPasien->id,
            'terapis_sesi_id' => $request->terapis_sesi_id,
            'status' => 'pending',
            'bukti_transfer_booking_path' => $path,
            'bukti_transfer_booking_mime' => $mime,
        ]);

        $slots = (int) $request->slots;
        $patientServiceOverrides = $request->input('patient_services', []);

        if ($slots === 1) {
            // Individual Mode
            $assignedLayanan = ! empty($patientServiceOverrides[0]) ? $patientServiceOverrides[0] : ($serviceIds[0] ?? null);
            if (! $assignedLayanan) {
                return redirect()->back()->with('error', 'Layanan belum dipilih.');
            }

            BookingPatient::create([
                'booking_id' => $booking->id,
                'pasien_id' => $primaryPasien->id,
                'layanan_id' => $assignedLayanan,
                'keluhan_pasien' => $request->complaint_main,
                'status_pasien' => 'menunggu',
            ]);
        } else {
            // Group Mode
            $names = $request->input('patient_names', []);
            $complaints = $request->input('patient_complaints', []);

            for ($i = 0; $i < $slots; $i++) {
                $assignedLayanan = ! empty($patientServiceOverrides[$i]) ? $patientServiceOverrides[$i] : ($serviceIds[0] ?? null);
                if (! $assignedLayanan) {
                    return redirect()->back()->with('error', 'Layanan belum lengkap untuk semua pasien.');
                }

                if ($i === 0) {
                    // Patient 1 (Primary)
                    BookingPatient::create([
                        'booking_id' => $booking->id,
                        'pasien_id' => $primaryPasien->id,
                        'layanan_id' => $assignedLayanan,
                        'keluhan_pasien' => $complaints[0] ?? null,
                        'status_pasien' => 'menunggu',
                    ]);
                } else {
                    // Patient 2, 3 (Secondary)
                    $extraName = $names[$i] ?? ('Pasien Tambahan '.($i + 1));
                    $extraComplaint = $complaints[$i] ?? null;

                    // Register extra guest patient
                    $extraUser = User::create([
                        'name' => $extraName,
                        'phone' => $primaryUser->phone.'-'.($i + 1).'-'.Str::random(3),
                        'password' => Hash::make('password123'),
                        'role' => UserRole::PATIENT,
                    ]);

                    $extraPasien = Pasien::create([
                        'user_id' => $extraUser->id,
                        'pasien_public_id' => 'PSN-'.strtoupper(Str::random(8)),
                        'nama_pasien' => $extraName,
                        'no_telp' => $extraUser->phone,
                    ]);

                    BookingPatient::create([
                        'booking_id' => $booking->id,
                        'pasien_id' => $extraPasien->id,
                        'layanan_id' => $assignedLayanan,
                        'keluhan_pasien' => $extraComplaint,
                        'status_pasien' => 'menunggu',
                    ]);
                }
            }
        }

        return redirect()->route('patient.booking.form-selesai')->with('success', 'Booking berhasil diajukan! Menunggu verifikasi.');
    }
}
