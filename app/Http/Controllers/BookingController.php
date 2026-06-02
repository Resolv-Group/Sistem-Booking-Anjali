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

    public function adminBookingForm(Request $request)
    {
        $therapists = Karyawan::where('peran', 'Terapis')
            ->where('status_karyawan', 'Aktif')
            ->with(['layanans', 'sessions' => function ($query) {
                $query->where('status', 'terbuka')
                    ->where('tanggal_sesi', '>=', now()->toDateString());
            }])
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => $t->nama_karyawan,
                    'image' => $t->fotoUrl() ?: 'https://i.pravatar.cc/150?u='.$t->id,
                    // Map layanan spesifik terapis ini
                    'services' => $t->layanans->map(function ($l) {
                        return [
                            'id' => $l->id,
                            'name' => $l->nama,
                            'price' => (int) $l->base_harga,
                            'homecare_price' => (int) $l->homecare_harga,
                            'discount' => (float) $l->diskon_persentase,
                            'desc' => $l->deskripsi ?: 'Layanan profesional.',
                        ];
                    }),
                    // Map sesi spesifik terapis ini
                    'sessions' => $t->sessions->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'tanggal_sesi' => $s->tanggal_sesi,
                            'waktu_mulai' => substr($s->waktu_mulai, 0, 5),
                            'kuota_sisa' => (int) $s->remaining_capacity,
                            'used_capacity' => (int) $s->used_capacity,
                        ];
                    }),
                ];
            });

        $patients = Pasien::select('id', 'nama_pasien', 'pasien_public_id', 'tanggal_lahir', 'email', 'no_telp')
            ->limit(10) // Ambil beberapa saja, sisanya via search (opsional)
            ->get();

        return view('pages.booking.admin.form', compact('therapists', 'patients'));
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

        $patients = Pasien::select('id', 'nama_pasien', 'pasien_public_id', 'tanggal_lahir', 'no_telp')
            ->orderBy('nama_pasien')
            ->get();

        return view('pages.booking.patient.form', compact('therapist', 'services', 'sessions', 'patients'));
    }

    public function quickRegisterPatient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->dob), // DOB as default password
            'role' => UserRole::PATIENT,
        ]);

        $pasien = Pasien::create([
            'user_id' => $user->id,
            'nama_pasien' => $request->name,
            'no_telp' => $request->phone,
            'tanggal_lahir' => $request->dob,
        ]);

        return response()->json([
            'pasien_id' => $pasien->id,
            'nama_pasien' => $pasien->nama_pasien,
            'pasien_public_id' => $pasien->pasien_public_id,
            'tanggal_lahir' => $pasien->tanggal_lahir,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'terapis_sesi_id' => 'required|exists:terapis_sesi,id',
            'slots' => 'required|integer|min:1|max:5',
            'patients_data' => 'required|string',
            'payment_proof' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $primaryUser = auth()->user();
        if (! $primaryUser) {
            return redirect()->back()->with('error', 'Silakan login terlebih dahulu.');
        }
        $primaryPasien = $primaryUser->pasien;

        $patientsData = json_decode($request->patients_data, true);
        if (empty($patientsData)) {
            return redirect()->back()->with('error', 'Data pasien tidak valid.');
        }

        // Upload payment proof
        $path = null;
        $mime = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $path = $file->store('bukti_transfer', 'public');
            $mime = $file->getClientMimeType();
        }

        $booking = Booking::create([
            'booking_oleh_pasien_id' => $primaryPasien->id,
            'terapis_sesi_id' => $request->terapis_sesi_id,
            'status' => 'pending',
            'bukti_transfer_booking_path' => $path,
            'bukti_transfer_booking_mime' => $mime,
        ]);

        foreach ($patientsData as $i => $slotData) {
            $serviceIds = $slotData['services'] ?? [];

            if (empty($serviceIds)) {
                $booking->delete();

                return redirect()->back()->with('error', 'Semua pasien harus memiliki minimal satu layanan.');
            }

            if ($i === 0) {
                // Patient 1 is always the logged-in pasien
                $pasienId = $primaryPasien->id;
            } else {
                // Guests: create new user + pasien
                $phone = ! empty($slotData['phone']) ? $slotData['phone'] : ($primaryUser->phone.'-'.($i + 1).'-'.Str::random(3));
                $email = ! empty($slotData['email']) ? $slotData['email'] : null;

                $newUser = User::create([
                    'name' => $slotData['name'] ?? 'Pasien Tambahan '.($i + 1),
                    'email' => $email,
                    'phone' => $phone,
                    'password' => Hash::make(Carbon::parse($slotData['dob'])->format('d-m-Y')),
                    'role' => UserRole::PATIENT,
                ]);

                $newPasien = Pasien::create([
                    'user_id' => $newUser->id,
                    'nama_pasien' => $slotData['name'] ?? 'Pasien Tambahan '.($i + 1),
                    'no_telp' => $phone,
                    'tanggal_lahir' => ! empty($slotData['dob']) ? $slotData['dob'] : null,
                    'jenis_kelamin' => 'L',
                    'created_by' => $primaryUser->id,
                    'updated_by' => $primaryUser->id,
                ]);

                $pasienId = $newPasien->id;
            }

            // One BookingPatient row per service
            foreach ($serviceIds as $layananId) {
                BookingPatient::create([
                    'booking_id' => $booking->id,
                    'pasien_id' => $pasienId,
                    'layanan_id' => $layananId,
                    'keluhan_pasien' => $slotData['complaint'] ?? null,
                    'status_pasien' => 'menunggu',
                ]);
            }
        }

        return redirect()->route('patient.booking.form-selesai')
            ->with('success', 'Booking berhasil diajukan! Menunggu verifikasi.');
    }

    public function adminBookingStore(Request $request)
    {
        $request->validate([
            'terapis_sesi_id' => 'required|exists:terapis_sesi,id',
            'slots' => 'required|integer|min:1|max:5',
            'patients_data' => 'required|string',
        ]);

        // Parse the patients_data JSON
        $patientsData = json_decode($request->patients_data, true);

        if (empty($patientsData)) {
            return redirect()->back()->with('error', 'Data pasien tidak valid.');
        }

        // dd($patientsData);

        // Create the booking (no payment proof for admin — direct confirm or pending)
        $booking = Booking::create([
            'booking_oleh_pasien_id' => null, // admin booking, no primary patient
            'terapis_sesi_id' => $request->terapis_sesi_id,
            'status' => 'approved', // admin bookings skip payment verification
            'booking_oleh_karyawan_id' => $request->admin_id,
            'approved_at' => now(),
            'approved_by' => $request->admin_id,
        ]);

        foreach ($patientsData as $i => $slotData) {
            $serviceIds = $slotData['services'] ?? [];

            // Each patient must have at least one service
            if (empty($serviceIds)) {
                $booking->delete();

                return redirect()->back()->with('error', 'Semua pasien harus memiliki minimal satu layanan.');
            }

            $pasienId = null;

            if ($slotData['type'] === 'terdaftar' && ! empty($slotData['id'])) {
                // Existing registered patient
                $pasienId = $slotData['id'];

            } else {
                // New patient — create user + pasien record
                $phone = $slotData['phone'] ?? ('admin-'.Str::random(6));
                $email = ! empty($slotData['email']) ? $slotData['email'] : null;

                $newUser = User::create([
                    'name' => $slotData['name'] ?? 'Pasien Baru',
                    'email' => $email,
                    'phone' => $phone,
                    'password' => Hash::make(Carbon::parse($slotData['dob'])->format('d-m-Y')),
                    'role' => UserRole::PATIENT,
                ]);

                $newPasien = Pasien::create([
                    'user_id' => $newUser->id,
                    'nama_pasien' => $slotData['name'] ?? 'Pasien Baru',
                    'no_telp' => $phone,
                    'email' => $email,
                    'tanggal_lahir' => ! empty($slotData['dob']) ? $slotData['dob'] : null,
                ]);

                $pasienId = $newPasien->id;
            }

            // Create one BookingPatient row per service per patient
            foreach ($serviceIds as $layananId) {
                BookingPatient::create([
                    'booking_id' => $booking->id,
                    'pasien_id' => $pasienId,
                    'layanan_id' => $layananId,
                    'keluhan_pasien' => $slotData['complaint'] ?? null,
                    'status_pasien' => 'menunggu',
                ]);
            }
        }

        return redirect()
            ->route('admin.booking.form-selesai')
            ->with('success', 'Booking berhasil dibuat.');
    }
}
