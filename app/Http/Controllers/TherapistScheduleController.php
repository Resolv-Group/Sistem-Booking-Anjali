<?php

namespace App\Http\Controllers;

use App\Models\TherapistSchedule;
use App\Models\TherapistSession;
use App\Models\Operasional;
use App\Services\SessionGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TherapistScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Asumsi user yang login adalah terapis

        // Ambil data karyawan terkait terapis
        $karyawan = $user->karyawan;
        $karyawanId = $karyawan ? $karyawan->id : $user->id;
        $kolaborasiId = $karyawan ? $karyawan->kolaborasi_id : 1;

        // Ambil jadwal operasional klinik cabang terkait
        $clinicOps = Operasional::where('kolaborasi_id', $kolaborasiId)->get()->keyBy('hari');

        // Ambil jadwal yang sudah ada di DB (coba Karyawan ID, fallback ke User ID)
        $existingSchedules = TherapistSchedule::where('terapis_id', $karyawanId)
            ->where('status', 'Aktif')
            ->orderBy('waktu_mulai', 'asc')
            ->get();
        if ($existingSchedules->isEmpty() && $karyawan && $karyawanId !== $user->id) {
            $existingSchedules = TherapistSchedule::where('terapis_id', $user->id)
                ->where('status', 'Aktif')
                ->orderBy('waktu_mulai', 'asc')
                ->get();
        }

        $groupedSchedules = $existingSchedules->groupBy('hari');
        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $formattedDays = [];

        foreach (range(1, 7) as $dayNum) {
            $dayName = $dayNames[$dayNum];
            $schedulesForDay = $groupedSchedules->get($dayNum);
            $op = $clinicOps->get($dayNum);

            // Format jam operasional
            $clinicHours = 'Tutup';
            if ($op && strtolower($op->status_operasional) === 'buka') {
                $open = $op->waktu_buka ? date('H:i', strtotime($op->waktu_buka)) : '08:00';
                $close = $op->waktu_tutup ? date('H:i', strtotime($op->waktu_tutup)) : '17:00';
                $clinicHours = "$open - $close";
                if ($op->waktu_istirahat_mulai && $op->waktu_istirahat_selesai) {
                    $breakStart = date('H:i', strtotime($op->waktu_istirahat_mulai));
                    $breakEnd = date('H:i', strtotime($op->waktu_istirahat_selesai));
                    $clinicHours .= " (Istirahat: $breakStart - $breakEnd)";
                }
            }

            if ($schedulesForDay && $schedulesForDay->isNotEmpty()) {
                // Suatu hari aktif jika salah satu sesinya berstatus 'Aktif'
                $isActive = $schedulesForDay->contains(function ($sched) {
                    return strtolower($sched->status) === 'aktif';
                });

                $slots = $schedulesForDay->map(function ($sched) {
                    $time = $sched->waktu_mulai;
                    if ($time) {
                        $time = date('H:i', strtotime($time));
                    }

                    return [
                        'id' => $sched->id,
                        'start' => $time ?? '',
                        'kuota' => (int) $sched->kuota,
                    ];
                })->toArray();

                if (empty($slots)) {
                    $slots = [['id' => 'temp_' . uniqid(), 'start' => '', 'kuota' => 0]];
                }

                $formattedDays[] = [
                    'name' => $dayName,
                    'active' => $isActive,
                    'clinic_hours' => $clinicHours,
                    'status_operasional' => $op ? $op->status_operasional : 'Tutup',
                    'open_time' => $op && $op->waktu_buka ? date('H:i', strtotime($op->waktu_buka)) : null,
                    'close_time' => $op && $op->waktu_tutup ? date('H:i', strtotime($op->waktu_tutup)) : null,
                    'break_start' => $op && $op->waktu_istirahat_mulai ? date('H:i', strtotime($op->waktu_istirahat_mulai)) : null,
                    'break_end' => $op && $op->waktu_istirahat_selesai ? date('H:i', strtotime($op->waktu_istirahat_selesai)) : null,
                    'slots' => $slots,
                ];
            } else {
                // DEFAULT: Jika belum ada di DB, card dimatikan (OFF)
                $formattedDays[] = [
                    'name' => $dayName,
                    'active' => false,
                    'clinic_hours' => $clinicHours,
                    'status_operasional' => $op ? $op->status_operasional : 'Tutup',
                    'open_time' => $op && $op->waktu_buka ? date('H:i', strtotime($op->waktu_buka)) : null,
                    'close_time' => $op && $op->waktu_tutup ? date('H:i', strtotime($op->waktu_tutup)) : null,
                    'break_start' => $op && $op->waktu_istirahat_mulai ? date('H:i', strtotime($op->waktu_istirahat_mulai)) : null,
                    'break_end' => $op && $op->waktu_istirahat_selesai ? date('H:i', strtotime($op->waktu_istirahat_selesai)) : null,
                    'slots' => [
                        ['id' => 'temp_' . uniqid(), 'start' => '', 'kuota' => 0],
                    ],
                ];
            }
        }

        return view('pages.jadwal.atur-jam-kerja', ['daysData' => $formattedDays]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        $karyawanId = $karyawan ? $karyawan->id : $user->id;
        $kolaborasiId = $karyawan ? $karyawan->kolaborasi_id : 1;

        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $daysInput = $request->input('days', []);
        $clinicOps = Operasional::where('kolaborasi_id', $kolaborasiId)->get()->keyBy('hari');

        // Validate kuota >= 1 and time scopes for active days/slots
        foreach ($daysInput as $item) {
            foreach ($item as $dayName => $dayData) {
                $isActive = isset($dayData['active']) && $dayData['active'] === '1';
                
                // Find matching day index
                $dayNum = array_search($dayName, $dayNames);
                $op = $dayNum ? $clinicOps->get($dayNum) : null;

                if ($isActive && isset($dayData['slots'])) {
                    foreach ($dayData['slots'] as $slot) {
                        if (!empty($slot['start'])) {
                            $kuota = isset($slot['kuota']) ? (int)$slot['kuota'] : 0;
                            if ($kuota < 1) {
                                return back()->withInput()->with('error', 'Kuota pasien untuk setiap sesi aktif minimal harus 1.');
                            }

                            // Validate clinic operational hours
                            if ($op) {
                                if (strtolower($op->status_operasional) === 'tutup') {
                                    return back()->withInput()->with('error', "Klinik tutup pada hari {$dayName}.");
                                }

                                $timeOnly = date('H:i', strtotime($slot['start']));

                                $open = $op->waktu_buka ? date('H:i', strtotime($op->waktu_buka)) : null;
                                $close = $op->waktu_tutup ? date('H:i', strtotime($op->waktu_tutup)) : null;

                                if ($open && $close) {
                                    if ($timeOnly < $open || $timeOnly >= $close) {
                                        return back()->withInput()->with('error', "Sesi jam {$timeOnly} pada hari {$dayName} berada di luar jam operasional klinik ({$open} - {$close}).");
                                    }
                                }

                                $breakStart = $op->waktu_istirahat_mulai ? date('H:i', strtotime($op->waktu_istirahat_mulai)) : null;
                                $breakEnd = $op->waktu_istirahat_selesai ? date('H:i', strtotime($op->waktu_istirahat_selesai)) : null;

                                if ($breakStart && $breakEnd) {
                                    if ($timeOnly >= $breakStart && $timeOnly < $breakEnd) {
                                        return back()->withInput()->with('error', "Sesi jam {$timeOnly} pada hari {$dayName} berada pada jam istirahat klinik ({$breakStart} - {$breakEnd}).");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach (range(1, 7) as $dayNum) {
            $dayName = $dayNames[$dayNum];

            // Temukan data input untuk hari ini
            $dayData = null;
            foreach ($daysInput as $item) {
                if (isset($item[$dayName])) {
                    $dayData = $item[$dayName];
                    break;
                }
            }

            // Ambil existing schedules dari DB untuk hari ini
            $existingSchedules = TherapistSchedule::where('terapis_id', $karyawanId)
                ->where('hari', $dayNum)
                ->get();
            if ($existingSchedules->isEmpty() && $karyawan && $karyawanId !== $user->id) {
                $existingSchedules = TherapistSchedule::where('terapis_id', $user->id)
                    ->where('hari', $dayNum)
                    ->get();
            }

            $isActive = $dayData && isset($dayData['active']) && $dayData['active'] === '1';

            if ($isActive && isset($dayData['slots'])) {
                $slots = $dayData['slots'];
                $processedIds = [];

                foreach ($slots as $slot) {
                    $start = $slot['start'] ?? null;
                    $kuota = isset($slot['kuota']) ? (int) $slot['kuota'] : 0;

                    if (! $start) {
                        continue;
                    }

                    $formattedStart = date('H:i:s', strtotime($start));
                    // Find existing schedule for this exact start time
                    $schedule = $existingSchedules->firstWhere('waktu_mulai', $formattedStart);

                    if ($schedule) {
                        // Update existing schedule (e.g., kuota, status)
                        $schedule->update([
                            'kuota' => $kuota,
                            'status' => 'Aktif',
                        ]);
                    } else {
                        // Create a new schedule entry for this slot
                        $schedule = TherapistSchedule::create([
                            'terapis_id' => $karyawanId,
                            'hari' => $dayNum,
                            'waktu_mulai' => $formattedStart,
                            'kuota' => $kuota,
                            'status' => 'Aktif',
                        ]);
                    }

                    // Track processed schedule IDs for later cleanup
                    $processedIds[] = $schedule->id;

                    // Generate sessions for this schedule (will not duplicate due to firstOrCreate)
                    SessionGenerator::generate($schedule);
                }

                // After processing slots, deactivate leftover schedules
                $deactivatedIds = [];
                foreach ($existingSchedules as $oldSchedule) {
                    if (! in_array($oldSchedule->id, $processedIds)) {
                        $oldSchedule->update(['status' => 'Tidak Aktif']);
                        $deactivatedIds[] = $oldSchedule->id;
                    }
                }

                if (! empty($deactivatedIds)) {
                    TherapistSession::whereIn('operasional_id', $deactivatedIds)
                        ->where('tanggal_sesi', '>', now()->toDateString())
                        ->whereDoesntHave('bookings')
                        ->update(['status' => 'dibatalkan']);
                }
            } else {
                // Nonaktifkan semua schedule hari ini
                foreach ($existingSchedules as $schedule) {
                    $schedule->update([
                        'status' => 'Tidak Aktif',
                    ]);
                }

                // When a day is turned OFF, we deactivate its schedule entries and cancel any future, unbooked sessions.
                // This ensures bookings that already exist are retained, while empty slots are marked as cancelled.
                $deactivatedOperasionalIds = $existingSchedules->pluck('id')->toArray();
                if (! empty($deactivatedOperasionalIds)) {
                    TherapistSession::whereIn('operasional_id', $deactivatedOperasionalIds)
                    ->where('tanggal_sesi', '>', now()->toDateString())
                    ->whereDoesntHave('bookings')
                    ->update(['status' => 'dibatalkan']);
                }
            }
        }

        return back()->with('success', 'Jadwal operasional terapis berhasil diperbarui.');
    }
}
