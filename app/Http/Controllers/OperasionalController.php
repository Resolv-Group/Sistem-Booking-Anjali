<?php

namespace App\Http\Controllers;

use App\Models\Kolaborasi;
use App\Models\Operasional;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OperasionalController extends Controller
{
    /**
     * Show the operational schedule settings for a branch.
     */
    public function index($id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);

        $schedules = Operasional::where('kolaborasi_id', $id_kolaborasi)
            ->orderBy('hari')
            ->get();

        // Initialize defaults if no schedules exist (e.g. newly created branch)
        if ($schedules->isEmpty()) {
            $now = Carbon::now();
            $defaults = [];
            for ($h = 1; $h <= 7; $h++) {
                $isSunday = ($h === 7);
                $defaults[] = [
                    'kolaborasi_id' => $id_kolaborasi,
                    'hari' => $h,
                    'waktu_buka' => $isSunday ? null : '08:00:00',
                    'waktu_tutup' => $isSunday ? null : '17:00:00',
                    'status_operasional' => $isSunday ? 'Tutup' : 'Buka',
                    'waktu_istirahat_mulai' => $isSunday ? null : '12:00:00',
                    'waktu_istirahat_selesai' => $isSunday ? null : '13:00:00',
                    'created_by' => auth()->id() ?: 1,
                    'updated_by' => auth()->id() ?: 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            Operasional::insert($defaults);
            $schedules = Operasional::where('kolaborasi_id', $id_kolaborasi)
                ->orderBy('hari')
                ->get();
        }

        $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $daysData = [];

        foreach ($schedules as $sched) {
            $dayIndex = $sched->hari - 1;
            
            // Map status Buka/Tutup to boolean for Alpine.js active state
            // Note: DB column is enum 'Buka'/'Tutup', and casts as boolean maps it if true/false is sent.
            // But we check both representation types to be highly robust.
            $isActive = false;
            if (is_bool($sched->status_operasional)) {
                $isActive = $sched->status_operasional;
            } else {
                $isActive = strtolower($sched->status_operasional) === 'buka';
            }

            $daysData[] = [
                'hari' => $sched->hari,
                'name' => $dayNames[$dayIndex] ?? '',
                'active' => $isActive,
                'open' => $sched->waktu_buka ? Carbon::parse($sched->waktu_buka)->format('H:i') : '08:00',
                'close' => $sched->waktu_tutup ? Carbon::parse($sched->waktu_tutup)->format('H:i') : '17:00',
                'hasBreak' => !empty($sched->waktu_istirahat_mulai),
                'breakStart' => $sched->waktu_istirahat_mulai ? Carbon::parse($sched->waktu_istirahat_mulai)->format('H:i') : '12:00',
                'breakEnd' => $sched->waktu_istirahat_selesai ? Carbon::parse($sched->waktu_istirahat_selesai)->format('H:i') : '13:00',
            ];
        }

        return view('pages.cabang.menu.operasional-jadwal', compact('kolaborasi', 'daysData'));
    }

    /**
     * Update the operational schedules for a branch.
     */
    public function update(Request $request, $id_kolaborasi)
    {
        $kolaborasi = Kolaborasi::findOrFail($id_kolaborasi);

        $request->validate([
            'days' => 'required|array|size:7',
            'days.*.hari' => 'required|integer|between:1,7',
            'days.*.active' => 'required|in:0,1',
            'days.*.open' => 'nullable|string',
            'days.*.close' => 'nullable|string',
            'days.*.has_break' => 'required|in:0,1',
            'days.*.break_start' => 'nullable|string',
            'days.*.break_end' => 'nullable|string',
        ]);

        foreach ($request->input('days') as $dayData) {
            $active = (bool) $dayData['active'];
            $hasBreak = (bool) $dayData['has_break'];

            // Prepare times (normalize H:i to H:i:s or leave time values)
            $waktuBuka = $active && !empty($dayData['open']) ? $dayData['open'] . ':00' : null;
            $waktuTutup = $active && !empty($dayData['close']) ? $dayData['close'] . ':00' : null;
            $istirahatMulai = $active && $hasBreak && !empty($dayData['break_start']) ? $dayData['break_start'] . ':00' : null;
            $istirahatSelesai = $active && $hasBreak && !empty($dayData['break_end']) ? $dayData['break_end'] . ':00' : null;

            Operasional::updateOrCreate(
                [
                    'kolaborasi_id' => $kolaborasi->id,
                    'hari' => $dayData['hari'],
                ],
                [
                    'waktu_buka' => $waktuBuka,
                    'waktu_tutup' => $waktuTutup,
                    'status_operasional' => $active ? 'Buka' : 'Tutup',
                    'waktu_istirahat_mulai' => $istirahatMulai,
                    'waktu_istirahat_selesai' => $istirahatSelesai,
                    'updated_by' => auth()->id() ?: 1,
                ]
            );
        }

        return redirect()
            ->route('admin-global.operasional-jadwal', $kolaborasi->id)
            ->with('success', 'Jam operasional klinik berhasil diperbarui!');
    }
}
