<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Models\BookingPatient;
use App\Models\TherapistSession;


class TherapistController extends Controller
{
    /**
     * Therapist dashboard with real data.
     */
    public function dashboard()
    {
        $user = auth()->user();
        $therapist = $user->karyawan;

        if (!$therapist) {
            return redirect()->route('landing');
        }

        $today = now()->toDateString();

        // --- Stats: sessions today ---
        $todaySessions = TherapistSession::where('terapis_id', $therapist->id)
            ->where('tanggal_sesi', $today)
            ->with(['bookings' => fn($q) => $q->whereIn('status', ['approved', 'completed'])->with('bookingPatients')])
            ->get();

        // Count unique booking_patients for today
        $totalSesiHariIni = $todaySessions->flatMap(fn($s) => $s->bookings)->flatMap(fn($b) => $b->bookingPatients)->count();
        $sesiSelesai = $todaySessions->flatMap(fn($s) => $s->bookings)->flatMap(fn($b) => $b->bookingPatients)->where('status_pasien', 'selesai')->count();

        // --- Active Session (sedang_berjalan) ---
        $activeBp = BookingPatient::whereHas('booking.session', fn($q) => $q->where('terapis_id', $therapist->id))
            ->where('status_pasien', 'sedang_berjalan')
            ->with(['pasien', 'layanan', 'booking.session'])
            ->first();

        $activeSession = null;
        if ($activeBp) {
            $diff = $activeBp->started_at ? Carbon::parse($activeBp->started_at)->diff(now()) : null;
            $durasiText = null;
            if ($diff) {
                $durasiText = $diff->h > 0
                    ? sprintf('%02d:%02d:%02d', $diff->h, $diff->i, $diff->s)
                    : sprintf('00:%02d:%02d', $diff->i, $diff->s);
            }

                $activeLayanan = BookingPatient::where('booking_id', $activeBp->booking_id)
        ->where('pasien_id', $activeBp->pasien_id)
        ->with('layanan')
        ->get()
        ->pluck('layanan.nama')
        ->filter()
        ->implode(', ');


            $activeSession = [
                'bp_id'     => $activeBp->id,
                'nama'      => $activeBp->pasien?->nama_pasien ?? 'Pasien',
                'layanan'   => $activeLayanan ?: 'Terapi',
                'public_id' => $activeBp->pasien?->pasien_public_id ?? '-',
                'durasi'    => $durasiText ?? '00:00:00',
            ];
        }

        // --- Upcoming schedule (menunggu status, today, after now) ---
        $upcomingSessions = TherapistSession::where('terapis_id', $therapist->id)
            ->where('tanggal_sesi', $today)
            ->where('waktu_mulai', '>=', now()->format('H:i:s'))
            ->with(['bookings' => fn($q) => $q->whereIn('status', ['approved'])->with(['bookingPatients.pasien', 'bookingPatients.layanan'])])
            ->orderBy('waktu_mulai')
            ->take(5)
            ->get();

        $upcoming = [];
        foreach ($upcomingSessions as $sess) {
            foreach ($sess->bookings as $booking) {
                $grouped = $booking->bookingPatients
                    ->where('status_pasien', 'menunggu')
                    ->groupBy('pasien_id');
                foreach ($grouped as $pasienId => $bpGroup) {
                    $first = $bpGroup->first();
                    $layananNames = $bpGroup->pluck('layanan.nama')->filter()->implode(', ');
                    $upcoming[] = [
                        'time'    => Carbon::parse($sess->waktu_mulai)->format('H:i'),
                        'name'    => $first->pasien?->nama_pasien ?? 'Pasien',
                        'type'    => $layananNames ?: 'Terapi',
                        'status'  => $booking->status, // 'approved'
                    ];
                }
            }
        }

        // Greeting based on current hour
        $hour = now()->hour;
        $greeting = match(true) {
            $hour < 11  => 'Selamat Pagi',
            $hour < 15  => 'Selamat Siang',
            $hour < 18  => 'Selamat Sore',
            default     => 'Selamat Malam',
        };

        // Therapist photo
        $fotoUrl = $therapist->foto
            ? 'data:' . ($therapist->foto_mime ?? 'image/jpeg') . ';base64,' . $therapist->foto
            : asset('images/logo_anjali.jpg');

        return view('pages.dashboard.therapist', compact(
            'therapist',
            'totalSesiHariIni',
            'sesiSelesai',
            'activeSession',
            'upcoming',
            'greeting',
            'fotoUrl'
        ));
    }

    /**
     * Layanan yang di-assign ke terapis yang sedang login.
     */
    public function myLayanan()
    {
        $user = auth()->user();
        $therapist = $user->karyawan;

        if (!$therapist) {
            return redirect()->route('landing');
        }

        $layanans = $therapist->layanans()->get();

        return view('pages.therapist.layanan', compact('layanans', 'therapist'));
    }

    public function MyPatientTherapist()
    {
        $user = auth()->user();
        $therapist = $user->karyawan;

        // Ambil Pasien yang memiliki setidaknya satu booking pada sesi terapis ini
        $patients = Pasien::whereHas('bookingPatients.booking.session', function ($q) use ($therapist) {
            $q->where('terapis_id', $therapist->id);
        })
        ->with(['bookingPatients' => function ($q) use ($therapist) {
            // Eager load booking + session so we can get tanggal_sesi
            $q->whereHas('booking.session', function ($sq) use ($therapist) {
                $sq->where('terapis_id', $therapist->id);
            })
            ->with(['booking.session'])
            ->latest();
        }])
        ->get()
        ->map(function ($p) {
            $lastBookingPatient = $p->bookingPatients->first();
            $lastSessionDate = optional($lastBookingPatient?->booking?->session)->tanggal_sesi;

            return [
                'id_raw'       => $p->id,
                'public_id'    => $p->pasien_public_id,
                'nama'         => $p->nama_pasien,
                'foto'         => $p->foto
                    ? 'data:' . ($p->foto_mime ?? 'image/jpeg') . ';base64,' . $p->foto
                    : asset('images/logo_anjali.jpg'),
                'last_session' => $lastSessionDate
                    ? \Carbon\Carbon::parse($lastSessionDate)->translatedFormat('d M Y')
                    : '-',
                'total_sesi'   => $p->bookingPatients->unique('booking_id')->count(),
                // Strip non-digits so wa.me links work correctly (e.g. 0812... → 62812...)
                'telepon'      => preg_replace('/\D/', '', ltrim($p->no_telp ?? '', '0')
                    ? '62' . ltrim(preg_replace('/\D/', '', $p->no_telp ?? ''), '0')
                    : ''),
            ];
        })
        ->values(); // ensure clean 0-indexed JSON array

        return view('pages.patient.my-patient-therapist', compact('patients'));
    }

    public function patientHistory($id)
    {
        $user = auth()->user();
        $therapist = $user->karyawan;

        $patient = Pasien::findOrFail($id);

        $records = BookingPatient::where('pasien_id', $id)
            ->whereHas('booking.session', function ($q) use ($therapist) {
                $q->where('terapis_id', $therapist->id);
            })
            ->with(['booking.session', 'layanan', 'rekamMedis'])
            ->latest()
            ->get()
            ->groupBy('booking_id')
            ->map(function ($group) {
                $first = $group->first();
                $session = $first->booking->session;

                $layananNames = $group->pluck('layanan.nama')->filter()->implode(', ');
                $keluhan = $group->pluck('keluhan_pasien')->filter()->unique()->implode(' | ');
                $catatan = $group->map(fn($bp) => optional($bp->rekamMedis)->catatan_terapis)
                    ->filter()->unique()->implode(' | ');

                return [
                    'id_raw'    => $first->id,
                    'id_alias'  => 'REG-' . str_pad($first->booking_id, 5, '0', STR_PAD_LEFT),
                    'tanggal'   => \Carbon\Carbon::parse($session->tanggal_sesi)->translatedFormat('d F Y'),
                    'jam'       => substr($session->waktu_mulai, 0, 5),
                    'layanan'   => $layananNames,
                    'keluhan'   => $keluhan ?: 'Tidak ada keluhan tertulis.',
                    'catatan'   => $catatan ?: 'Belum ada catatan medis.',
                    'ringkasan' => $first->ringkasan_sesi ?? 'Belum ada ringkasan.',
                    'status'    => $first->status_pasien,
                ];
            })
            ->values();

        return view('pages.patient.history.therapist', compact('patient', 'records'));
    }
}
