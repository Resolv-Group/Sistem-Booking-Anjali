<?php

namespace App\Http\Controllers;

use App\Models\BookingPatient;
use App\Models\RekamMedis;
use App\Models\TherapistSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TherapistSessionController extends Controller
{
    /**
     * Show the therapist session agenda/schedule.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->route('landing')->with('error', 'Akses ditolak. Anda bukan terapis terdaftar.');
        }

        // Get selected date or default to today
        $selectedDate = $request->input('date', now()->toDateString());

        // Retrieve sessions for the logged-in therapist on the selected date
        $dbSessions = TherapistSession::where('terapis_id', $karyawan->id)->where('tanggal_sesi', $selectedDate)->orderBy('waktu_mulai')->get();

        $sessions = [];

        foreach ($dbSessions as $sess) {
            $patients = [];

            // Loop through approved/completed bookings for this session
            foreach ($sess->bookings as $booking) {
                if (!in_array($booking->status, ['approved', 'completed'])) {
                    continue;
                }

                // Determine if this booking is a "group" (multiple distinct patients)
                $distinctPatientIds = $booking->bookingPatients->pluck('pasien_id')->unique();
                $isGroup = $distinctPatientIds->count() > 1;

                // Group booking_pasien rows by pasien_id
                $grouped = $booking->bookingPatients->groupBy('pasien_id');

                foreach ($grouped as $pasienId => $bpRows) {
                    // Pick the "primary" row for status/timing (e.g. first one, or ongoing if any)
                    $primaryBp = $bpRows->firstWhere('status_pasien', 'sedang_berjalan') ?? $bpRows->first();

                    $durationText = null;
                    if ($primaryBp->status_pasien === 'sedang_berjalan' && $primaryBp->started_at) {
                        $diff = Carbon::parse($primaryBp->started_at)->diff(now());
                        $durationText = match (true) {
                            $diff->h > 0 => "{$diff->h} jam {$diff->i} menit berjalan",
                            $diff->i > 0 => "{$diff->i} menit berjalan",
                            default => 'Baru dimulai',
                        };
                    }

                    // Collect all layanan names for this patient in this booking
                    $layananList = $bpRows->map(fn($bp) => $bp->layanan?->nama ?? 'Terapi')->filter()->implode(', ');

                    // Determine overall status for this patient card:
                    // selesai only if ALL their rows are selesai
                    $allSelesai = $bpRows->every(fn($bp) => $bp->status_pasien === 'selesai');
                    $anyOngoing = $bpRows->contains('status_pasien', 'sedang_berjalan');
                    $overallStatus = $allSelesai ? 'selesai' : ($anyOngoing ? 'sedang_berjalan' : 'menunggu');

                    // Merge ringkasan from all rows
                    $ringkasan = $bpRows->pluck('ringkasan_sesi')->filter()->implode(' | ');

                    $patients[] = [
                        'id' => $primaryBp->id,
                        'booking_id' => $booking->id,
                        'name' => $primaryBp->pasien?->nama_pasien ?? 'Pasien',
                        'type' => $layananList, // e.g. "Fisioterapi, Akupuntur"
                        'complaint' => $primaryBp->keluhan_pasien ?: 'Tidak ada keluhan tertulis.',
                        'has_summary' => $bpRows->contains(fn($bp) => !empty($bp->ringkasan_sesi)),
                        'is_done' => $allSelesai,
                        'status_pasien' => $overallStatus,
                        'duration' => $durationText,
                        'ringkasan_sesi' => $ringkasan ?: null,
                        'is_group' => $isGroup, // true = group booking
                    ];
                }
            }

            // Determine status of the schedule slot based on patient statuses
            $status = 'waiting';
            $statusText = 'Menunggu';

            if (count($patients) > 0) {
                $hasOngoing = collect($patients)->contains('status_pasien', 'sedang_berjalan');
                $allDone = collect($patients)->every('status_pasien', 'selesai');

                if ($hasOngoing) {
                    $status = 'ongoing';
                    $statusText = 'Sedang Berlangsung';
                } elseif ($allDone) {
                    $status = 'completed';
                    $statusText = 'Selesai';
                }
            }

            $waktuMulai = Carbon::parse($sess->waktu_mulai);
            $sessions[] = [
                'id' => $sess->id,
                'time_start' => $waktuMulai->format('H:i'),
                'time_end' => $waktuMulai->copy()->addHour()->format('H:i'),
                'status' => $status,
                'status_text' => $statusText,
                'patients' => $patients,
            ];
        }

        $totalSessions = count($sessions);

        return view('pages.jadwal.therapist', compact('sessions', 'totalSessions', 'selectedDate'));
    }

    /**
     * Start the patient session (morph status to sedang_berjalan).
     */
    public function startSession($id)
    {
        $bp = BookingPatient::findOrFail($id);
        BookingPatient::where('booking_id', $bp->booking_id)
            ->where('pasien_id', $bp->pasien_id)
            ->update([
                'status_pasien' => 'sedang_berjalan',
                'started_at' => now(),
            ]);

        return redirect()
            ->back()
            ->with('success', "Sesi terapi untuk {$bp->pasien->nama_pasien} telah dimulai!");
    }

    /**
     * Show the medical documentation (Pencatatan) form.
     */
    public function catatanForm($id)
    {
        $bp = BookingPatient::with(['pasien', 'layanan', 'booking.session', 'rekamMedis', 'rekamMedis.fotos'])->findOrFail($id);

        $rekamMedis = $bp->rekamMedis;
        if (!$rekamMedis) {
            $rekamMedis = new RekamMedis();
        }

        $existingPhotos = [];
        if ($rekamMedis && $rekamMedis->exists && $rekamMedis->fotos) {
            foreach ($rekamMedis->fotos as $foto) {
                $existingPhotos[] = [
                    'id' => $foto->id,
                    'url' => 'data:' . $foto->foto_mime . ';base64,' . $foto->foto,
                    'file' => null
                ];
            }
        }

        return view('pages.jadwal.ringkasan-sesi', compact('bp', 'rekamMedis', 'existingPhotos'));
    }

    /**
     * Store notes draft or complete session.
     */
    public function saveCatatan(Request $request, $id)
    {
        $bp = BookingPatient::findOrFail($id);

        try {
            DB::transaction(function () use ($request, $bp) {
                // Rekam medis stays per-row (one per layanan) — no change here
                $rekamMedis = $bp->rekamMedis;
                if (!$rekamMedis) {
                    $rekamMedis = new RekamMedis(['booking_pasien_id' => $bp->id]);
                }

                $inputData = $request->except(['_token', 'ringkasan_sesi', 'action_type', 'status_pasien_action', 'status_pasien_radio', 'deleted_fotos', 'foto', 'golongan_darah', 'tinggi_badan', 'berat_badan']);
                $rekamMedis->fill($inputData);
                $rekamMedis->save();

                // Save physical stats to patient
                $pasien = $bp->pasien;
                if ($pasien) {
                    $patientUpdates = [];
                    if ($request->filled('golongan_darah')) {
                        $patientUpdates['golongan_darah'] = $request->input('golongan_darah');
                    }
                    if ($request->filled('tinggi_badan')) {
                        $patientUpdates['tinggi_badan'] = $request->input('tinggi_badan');
                    }
                    if ($request->filled('berat_badan')) {
                        $patientUpdates['berat_badan'] = $request->input('berat_badan');
                    }
                    if (!empty($patientUpdates)) {
                        $pasien->update($patientUpdates);
                    }
                }

                // Handle photo deletions
                if ($request->has('deleted_fotos')) {
                    \App\Models\RekamMedisFotos::whereIn('id', $request->input('deleted_fotos'))->delete();
                }

                if ($request->hasFile('foto')) {
                    foreach ($request->file('foto') as $file) {
                        if ($file->isValid()) {
                            $base64Data = base64_encode(file_get_contents($file->getRealPath()));
                            $mimeType = $file->getClientMimeType();

                            $rekamMedis->fotos()->create([
                                'foto' => $base64Data,
                                'foto_mime' => $mimeType,
                            ]);
                        }
                    }
                }

                $isComplete = $request->input('action_type') === 'complete' || $request->input('status_pasien_action') === 'complete';

                $updatePayload = [
                    'ringkasan_sesi' => $request->input('ringkasan_sesi'),
                ];

                if ($isComplete) {
                    $updatePayload['status_pasien'] = 'selesai';
                    $updatePayload['finished_at'] = now();
                } else {
                    $updatePayload['status_pasien'] = 'sedang_berjalan';
                }

                // Update all rows for this patient in this booking
                BookingPatient::where('booking_id', $bp->booking_id)->where('pasien_id', $bp->pasien_id)->update($updatePayload);

                // Check if all patients in the booking have finished
                $booking = $bp->booking;
                if ($booking) {
                    $allFinished = $booking->bookingPatients()->where('status_pasien', '!=', 'selesai')->count() === 0;

                    if ($allFinished) {
                        $booking->update([
                            'status' => 'completed',
                            'completed_by' => auth()->id(),
                            'completed_at' => now(),
                        ]);
                    }
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan rekam medis: ' . $e->getMessage())->withInput();
        }

        $isComplete = $request->input('action_type') === 'complete' || $request->input('status_pasien_action') === 'complete';
        $message = $isComplete ? "Sesi rekam medis untuk {$bp->pasien->nama_pasien} berhasil diselesaikan!" : 'Draft catatan berhasil disimpan.';

        return redirect()->route('therapist.jadwal')->with('success', $message);
    }
}
