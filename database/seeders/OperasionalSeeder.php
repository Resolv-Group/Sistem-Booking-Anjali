<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperasionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kolaborasiIds = DB::table('kolaborasi')->pluck('id');
        $now = Carbon::now();
        $data = [];

        foreach ($kolaborasiIds as $kolaborasiId) {
            for ($hari = 1; $hari <= 7; $hari++) {
                // Logic: Open Mon-Sat (1-6), Closed on Sunday (7)
                $isSunday = ($hari === 7);

                $data[] = [
                    'kolaborasi_id'      => $kolaborasiId,
                    'hari'               => $hari,
                    'waktu_buka'         => $isSunday ? null : '08:00:00',
                    'waktu_tutup'        => $isSunday ? null : '17:00:00',
                    'waktu_istirahat_mulai' => $isSunday ? null : '12:00:00',
                    'waktu_istirahat_selesai' => $isSunday ? null : '13:00:00',
                    'status_operasional' => $isSunday ? 'Tutup' : 'Buka',
                    'created_by'         => 1,
                    'updated_by'         => 1,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];
            }
        }

        // Using chunking or a single insert is much faster than individual inserts
        DB::table('operasional_rumah_terapi')->insert($data);
    }
}
