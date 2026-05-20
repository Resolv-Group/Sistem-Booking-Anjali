<?php

namespace App\Services;

use App\Models\TherapistSchedule;
use App\Models\TherapistSession;
use Carbon\Carbon;

class SessionGenerator
{
    public static function generate(
        TherapistSchedule $operasional,
        int $daysAhead = 30
    ) {
        $today = Carbon::today();

        for ($i = 0; $i <= $daysAhead; $i++) {
            $date = $today->copy()->addDays($i);

            if (
                $date->dayOfWeekIso
                != $operasional->hari
            ) {
                continue;
            }

            // Check if a session already exists for this slot
            $existing = TherapistSession::where([
                'operasional_id' => $operasional->id,
                'terapis_id' => $operasional->terapis_id,
                'tanggal_sesi' => $date->toDateString(),
                'waktu_mulai' => $operasional->waktu_mulai,
            ])->first();

            if ($existing) {
                // Only restore status if the session has no bookings
                // (booked sessions should keep their current status)
                if (! $existing->bookings()->exists()) {
                    $existing->update([
                        'kuota' => $operasional->kuota,
                        'status' => 'terbuka',
                    ]);
                }
            } else {
                TherapistSession::create([
                    'operasional_id' => $operasional->id,
                    'terapis_id' => $operasional->terapis_id,
                    'tanggal_sesi' => $date->toDateString(),
                    'waktu_mulai' => $operasional->waktu_mulai,
                    'kolaborasi_id' => $operasional->therapist?->kolaborasi_id ?? 1,
                    'kuota' => $operasional->kuota,
                    'status' => 'terbuka',
                ]);
            }
        }
    }
}
