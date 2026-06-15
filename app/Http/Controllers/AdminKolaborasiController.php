<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Booking;
use App\Models\BookingPatient;
use App\Models\Kolaborasi;
use App\Models\Layanan;
use App\Models\Pasien;
use App\Models\TherapistSchedule;
use Carbon\Carbon;

class AdminKolaborasiController extends Controller
{
    public function TherapistList()
    {
        $user = auth()->user();
        $cabangId = $user->karyawan->kolaborasi_id;

        // Ambil semua terapis yang bekerja di cabang ini
        $therapists = Karyawan::where('kolaborasi_id', $cabangId)
            ->where('peran', 'Terapis')
            ->with('kolaborasi')
            ->get()
            ->map(function ($p) {
            $noTelp = preg_replace('/\D/', '', $p->no_telp ?? '');
            return [
                'id_raw'     => $p->id,
                'nama'       => $p->nama_karyawan,
                'peran'      => $p->peran,
                'kolaborasi' => $p->kolaborasi->nama_kolaborasi ?? '-',
                'foto'       => $p->foto
                    ? 'data:' . ($p->foto_mime ?? 'image/jpeg') . ';base64,' . $p->foto
                    : asset('images/logo_anjali.jpg'),
                'telepon'    => $noTelp
                    ? '62' . ltrim($noTelp, '0')
                    : '',
            ];
        })
        ->values(); // ensure clean 0-indexed JSON array

        return view('pages.admin-kolaborasi.therapist-list', compact('therapists'));
    }

    public function TherapistDetail($id)
    {
        $user = auth()->user();
        $cabangId = $user->karyawan->kolaborasi_id;

        // Ensure the therapist is in the same branch
        $therapist = Karyawan::where('id', $id)
            ->where('kolaborasi_id', $cabangId)
            ->where('peran', 'Terapis')
            ->firstOrFail();

        // Get therapist's services
        $services = $therapist->layanans;

        // Get therapist's schedules, grouped by day
        $schedulesByDay = TherapistSchedule::where('terapis_id', $id)
            ->where('status', 'Aktif')
            ->orderBy('hari', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get()
            ->groupBy('hari')
            ->sortKeys();

        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        // Format photo
        $fotoUrl = $therapist->foto
            ? 'data:' . ($therapist->foto_mime ?? 'image/jpeg') . ';base64,' . $therapist->foto
            : asset('images/logo_anjali.jpg');

        return view('pages.admin-kolaborasi.therapist-detail', compact('therapist', 'services', 'schedulesByDay', 'dayNames', 'fotoUrl'));
    }

    public function dashboard()
    {
        $user = auth()->user();
        if (!$user || !$user->karyawan) {
            abort(403, 'Akses ditolak. Anda bukan admin cabang.');
        }

        $cabangId = $user->karyawan->kolaborasi_id;
        $today = now()->toDateString();

        // 1. Booking Hari Ini
        $bookingHariIni = Booking::whereHas('session', function ($query) use ($cabangId, $today) {
            $query->where('kolaborasi_id', $cabangId)
                  ->where('tanggal_sesi', $today);
        })
        ->whereIn('status', ['approved', 'completed'])
        ->count();

        // 2. Terapis Aktif
        $terapisAktif = Karyawan::where('kolaborasi_id', $cabangId)
            ->where('peran', 'Terapis')
            ->where('status_karyawan', 'Aktif')
            ->count();

        // 3. Total Layanan
        $totalLayanan = Layanan::where('kolaborasi_id', $cabangId)->count();

        // 4. Pending Approvals
        $pendingBookings = Booking::where('status', 'pending')
            ->whereHas('session', function ($query) use ($cabangId) {
                $query->where('kolaborasi_id', $cabangId);
            })
            ->with(['patient', 'bookingPatients.pasien', 'bookingPatients.layanan', 'session.therapist'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                $bookingPatients = $booking->bookingPatients;
                $uniquePatients = $bookingPatients->unique('pasien_id');
                $primaryPatient = $uniquePatients->first();
                $layananNames = $bookingPatients->pluck('layanan.nama')->filter()->unique()->implode(', ');

                return [
                    'id_raw' => $booking->id,
                    'id' => 'BK-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT),
                    'nama' => $primaryPatient->pasien->nama_pasien ?? ($booking->patient->nama_pasien ?? 'Unknown'),
                    'layanan' => $layananNames ?: 'Layanan',
                    'terapis' => $booking->session->therapist->nama_karyawan ?? 'Unknown',
                    'waktu' => Carbon::parse($booking->session->tanggal_sesi)->translatedFormat('d F Y') . ' • ' . substr($booking->session->waktu_mulai, 0, 5),
                    'bukti_transfer_url' => $booking->bukti_transfer_booking_path ? route('bukti-transfer.view', ['filename' => basename($booking->bukti_transfer_booking_path)]) : null,
                ];
            });

        return view('pages.dashboard.admin-cabang', compact('bookingHariIni', 'terapisAktif', 'totalLayanan', 'pendingBookings'));
    }

    public function PatientList(Request $request)
    {
        $search = $request->query('search', '');

        $patients = Pasien::when($search, function ($query) use ($search) {
                $query->where('nama_pasien', 'like', "%{$search}%")
                      ->orWhere('pasien_public_id', 'like', "%{$search}%")
                      ->orWhere('no_telp', 'like', "%{$search}%");
            })
            ->orderBy('nama_pasien', 'asc')
            ->get()
            ->map(function ($p) {
                return [
                    'id_raw'     => $p->id,
                    'public_id'  => $p->pasien_public_id,
                    'nama'       => $p->nama_pasien,
                    'membership' => $p->membership_tier ?? 'Basic',
                    'foto'       => $p->foto
                        ? 'data:' . ($p->foto_mime ?? 'image/jpeg') . ';base64,' . $p->foto
                        : asset('images/logo_anjali.jpg'),
                    'telepon'    => preg_replace('/\D/', '', $p->no_telp ?? '')
                        ? '62' . ltrim(preg_replace('/\D/', '', $p->no_telp ?? ''), '0')
                        : '',
                ];
            })
            ->values();

        return view('pages.admin-kolaborasi.patient-list', compact('patients', 'search'));
    }

    public function PatientDetail($id)
    {
        $patient = Pasien::findOrFail($id);

        $bookings = BookingPatient::where('pasien_id', $id)
            ->with(['booking.session.therapist', 'booking.session.kolaborasi', 'layanan'])
            ->latest()
            ->get()
            ->groupBy('booking_id')
            ->map(function ($group) {
                $first = $group->first();
                $booking = $first->booking;
                $session = $booking->session;
                
                $layananNames = $group->pluck('layanan.nama')->filter()->unique()->implode(', ');
                
                return [
                    'booking_id' => $booking->id,
                    'id' => 'BK-' . str_pad($booking->id, 5, '0', STR_PAD_LEFT),
                    'tanggal' => Carbon::parse($session->tanggal_sesi)->translatedFormat('d M Y'),
                    'jam' => substr($session->waktu_mulai, 0, 5),
                    'layanan' => $layananNames,
                    'terapis' => $session->therapist->nama_karyawan ?? 'Unknown',
                    'status' => $booking->status,
                ];
            })
            ->values();

        $medicalRecords = BookingPatient::where('pasien_id', $id)
            ->whereHas('rekamMedis')
            ->with(['booking.session.therapist', 'layanan', 'rekamMedis'])
            ->latest()
            ->get()
            ->groupBy('booking_id')
            ->map(function ($group) {
                $first = $group->first();
                $session = $first->booking->session;
                $rekamMedis = $first->rekamMedis;

                $layananNames = $group->pluck('layanan.nama')->filter()->unique()->implode(', ');

                return [
                    'tanggal' => Carbon::parse($session->tanggal_sesi)->translatedFormat('d M Y'),
                    'jam' => substr($session->waktu_mulai, 0, 5),
                    'layanan' => $layananNames,
                    'terapis' => $session->therapist->nama_karyawan ?? 'Unknown',
                    'keluhan' => $first->keluhan_pasien ?? '-',
                    'catatan_terapis' => $rekamMedis->catatan_terapis ?? '-',
                    'saran' => $rekamMedis->saran_rekomendasi ?? '-',
                    'skala_nyeri' => $rekamMedis->skala_nyeri ?? '-',
                    'tensi' => $rekamMedis->tensi_sys ? "{$rekamMedis->tensi_sys}/{$rekamMedis->tensi_dia} mmHg" : '-',
                ];
            })
            ->values();

        $fotoUrl = $patient->foto
            ? 'data:' . ($patient->foto_mime ?? 'image/jpeg') . ';base64,' . $patient->foto
            : asset('images/logo_anjali.jpg');

        return view('pages.admin-kolaborasi.patient-detail', compact('patient', 'bookings', 'medicalRecords', 'fotoUrl'));
    }

    public function layananIndex()
    {
        $user = auth()->user();
        $cabangId = $user->karyawan->kolaborasi_id;
        $kolaborasi = Kolaborasi::findOrFail($cabangId);

        $layanans = Layanan::where('kolaborasi_id', $cabangId)
            ->orderBy('nama')
            ->get();

        return view('pages.admin-kolaborasi.layanan.index', compact('kolaborasi', 'layanans'));
    }

    public function layananCreate()
    {
        $user = auth()->user();
        $cabangId = $user->karyawan->kolaborasi_id;
        $kolaborasi = Kolaborasi::findOrFail($cabangId);

        return view('pages.admin-kolaborasi.layanan.create', compact('kolaborasi'));
    }

    public function layananStore(Request $request)
    {
        $user = auth()->user();
        $cabangId = $user->karyawan->kolaborasi_id;

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'base_harga' => 'required|numeric|min:0',
            'homecare_harga' => 'nullable|numeric|min:0',
            'diskon_persentase' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:Tersedia,Tidak Tersedia',
        ]);

        $validated['kolaborasi_id'] = $cabangId;
        $validated['created_by'] = $user->id;

        Layanan::create($validated);

        return redirect()
            ->route('admin-cabang.layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan!');
    }

    public function layananEdit($id)
    {
        $user = auth()->user();
        $cabangId = $user->karyawan->kolaborasi_id;
        $kolaborasi = Kolaborasi::findOrFail($cabangId);

        $layanan = Layanan::where('kolaborasi_id', $cabangId)
            ->findOrFail($id);

        return view('pages.admin-kolaborasi.layanan.edit', compact('kolaborasi', 'layanan'));
    }

    public function layananUpdate(Request $request, $id)
    {
        $user = auth()->user();
        $cabangId = $user->karyawan->kolaborasi_id;

        $layanan = Layanan::where('kolaborasi_id', $cabangId)
            ->findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'base_harga' => 'required|numeric|min:0',
            'homecare_harga' => 'nullable|numeric|min:0',
            'diskon_persentase' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:Tersedia,Tidak Tersedia',
        ]);

        $validated['updated_by'] = $user->id;

        $layanan->update($validated);

        return redirect()
            ->route('admin-cabang.layanan.index')
            ->with('success', 'Layanan berhasil diperbarui!');
    }

    public function layananDestroy($id)
    {
        $user = auth()->user();
        $cabangId = $user->karyawan->kolaborasi_id;

        $layanan = Layanan::where('kolaborasi_id', $cabangId)
            ->findOrFail($id);

        $layanan->delete();

        return redirect()
            ->route('admin-cabang.layanan.index')
            ->with('success', 'Layanan berhasil dihapus!');
    }

    public function bookingHistory()
    {
        $user = auth()->user();
        if (!$user || !$user->karyawan) {
            abort(403, 'Akses ditolak.');
        }

        $cabangId = $user->karyawan->kolaborasi_id;

        // Fetch all non-pending bookings (history = approved, completed, rejected, cancelled)
        $bookings = Booking::whereHas('session', function ($query) use ($cabangId) {
            $query->where('kolaborasi_id', $cabangId);
        })
            ->whereIn('status', ['approved', 'completed', 'rejected', 'cancelled'])
            ->with(['patient', 'bookingPatients.pasien', 'bookingPatients.layanan', 'session.therapist'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $mappedBookings = $bookings->map(function ($booking) {
            $bookingPatients = $booking->bookingPatients;
            $uniquePatients = $bookingPatients->unique('pasien_id');
            $uniquePatientCount = $uniquePatients->count();
            $primaryPatient = $uniquePatients->first();
            $extraPatients = $uniquePatients->slice(1);
            $layananNames = $bookingPatients->pluck('layanan.nama')->filter()->unique()->implode(', ');

            // Map status to Indonesian labels
            $statusMap = [
                'approved' => 'disetujui',
                'completed' => 'selesai',
                'rejected' => 'ditolak',
                'cancelled' => 'dibatalkan',
            ];

            return [
                'id_raw' => $booking->id,
                'id' => str_pad($booking->id, 4, '0', STR_PAD_LEFT),
                'nama' => $primaryPatient->pasien->nama_pasien ?? ($booking->patient->nama_pasien ?? 'Unknown'),
                'patient_id' => $primaryPatient->pasien_id ?? ($booking->booking_oleh_pasien_id ?? null),
                'status' => $statusMap[$booking->status] ?? $booking->status,
                'tipe' => $uniquePatientCount > 1 ? 'Group' : 'Personal',
                'extra' => max(0, $uniquePatientCount - 1),
                'peserta' => $extraPatients->map(fn($bp) => [
                    'id' => $bp->pasien_id,
                    'nama' => $bp->pasien->nama_pasien ?? 'Pasien',
                ])->values()->all(),
                'showPeserta' => false,
                'info' => ($booking->session->therapist->nama_karyawan ?? 'Unknown') . ' • ' . ($layananNames ?: 'Layanan'),
                'waktu' => Carbon::parse($booking->session->tanggal_sesi)->translatedFormat('d F Y') . ' • ' . substr($booking->session->waktu_mulai, 0, 5),
                'alasan_status' => $booking->alasan_status,
            ];
        })->values();

        // Calculate stats for the current month
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $totalThisMonth = Booking::whereHas('session', function ($q) use ($cabangId) {
            $q->where('kolaborasi_id', $cabangId);
        })
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $approvedThisMonth = Booking::whereHas('session', function ($q) use ($cabangId) {
            $q->where('kolaborasi_id', $cabangId);
        })
            ->whereIn('status', ['approved', 'completed'])
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        $approvalRate = $totalThisMonth > 0
            ? round(($approvedThisMonth / $totalThisMonth) * 100, 1)
            : 0;

        $monthName = now()->translatedFormat('F');

        return view('pages.booking.admin.history', compact(
            'mappedBookings',
            'totalThisMonth',
            'approvalRate',
            'monthName'
        ));
    }

    public function KolaborasiProfile()
    {
        $user = auth()->user();
        $cabangId = $user->karyawan->kolaborasi_id;
        $kolaborasi = Kolaborasi::findOrFail($cabangId);

        $logoUrl = $kolaborasi->logo
            ? 'data:' . ($kolaborasi->logo_mime ?? 'image/jpeg') . ';base64,' . $kolaborasi->logo
            : asset('images/logo_anjali.jpg');

        return view('pages.admin-kolaborasi.profile-kolaborasi', compact('kolaborasi', 'logoUrl'));
    }

    public function KolaborasiProfileUpdate(Request $request)
    {
        $user = auth()->user();
        $cabangId = $user->karyawan->kolaborasi_id;
        $kolaborasi = Kolaborasi::findOrFail($cabangId);

        $validated = $request->validate([
            'nama_kolaborasi' => 'required|string|max:255',
            'alamat_kolaborasi' => 'nullable|string',
            'kota_kolaborasi' => 'nullable|string|max:100',
            'no_telp_kolaborasi' => 'nullable|string|max:50',
            'email_kolaborasi' => 'nullable|email|max:100',
            'homecare_harga' => 'required|numeric|min:0',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoBase64 = base64_encode(file_get_contents($logoFile->getRealPath()));
            $logoMime = $logoFile->getClientMimeType();
            $validated['logo'] = $logoBase64;
            $validated['logo_mime'] = $logoMime;
        }

        $validated['updated_by'] = $user->id;

        $kolaborasi->update($validated);

        return redirect()
            ->route('admin-cabang.kolaborasi.profile')
            ->with('success', 'Profil Kolaborasi berhasil diperbarui.');
    }
}
