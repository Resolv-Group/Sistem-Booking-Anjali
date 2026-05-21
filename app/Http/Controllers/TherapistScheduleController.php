<?php

namespace App\Http\Controllers;

use App\Models\TherapistSchedule;
use App\Models\TherapistSession;
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

        // dd($existingSchedules);

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
                    'clinic_hours' => $this->getClinicHours($dayNum),
                    'slots' => $slots,
                ];
            } else {
                // DEFAULT: Jika belum ada di DB, card dimatikan (OFF)
                $formattedDays[] = [
                    'name' => $dayName,
                    'active' => false,
                    'clinic_hours' => $this->getClinicHours($dayNum),
                    'slots' => [
                        ['id' => 'temp_' . uniqid(), 'start' => '', 'kuota' => 0],
                    ],
                ];
            }
        }

        return view('pages.jadwal.atur-jam-kerja', ['daysData' => $formattedDays]);
    }

    private function getClinicHours($day)
    {
        if ($day === 6) {
            return '08:00 - 17:00';
        }
        if ($day === 7) {
            return 'Tutup';
        }

        return '08:00 - 20:00';
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        $karyawanId = $karyawan ? $karyawan->id : $user->id;

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

        // Validate kuota >= 1 for active days/slots
        foreach ($daysInput as $item) {
            foreach ($item as $dayName => $dayData) {
                $isActive = isset($dayData['active']) && $dayData['active'] === '1';
                if ($isActive && isset($dayData['slots'])) {
                    foreach ($dayData['slots'] as $slot) {
                        if (!empty($slot['start'])) {
                            $kuota = isset($slot['kuota']) ? (int)$slot['kuota'] : 0;
                            if ($kuota < 1) {
                                return back()->withInput()->with('error', 'Kuota pasien untuk setiap sesi aktif minimal harus 1.');
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
