<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KolaborasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kolaborasi')->insert([
            [
                'id' => 1,
                'nama_kolaborasi' => 'Rumah Terapi Anjali',
                'alamat_kolaborasi' => 'Alamat Rumah Terapi Anjali',
                'kota_kolaborasi' => 'Surabaya Timur',
                'no_telp_kolaborasi' => '08111111111',
                'email_kolaborasi' => 'anjali@example.com',
                'created_by' => '1',
                'updated_by' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'nama_kolaborasi' => 'Lima Jari',
                'alamat_kolaborasi' => 'Alamat Lima Jari',
                'kota_kolaborasi' => 'Malang',
                'no_telp_kolaborasi' => '08222222222',
                'email_kolaborasi' => 'limajari@example.com',
                'created_by' => '1',
                'updated_by' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // Keep PostgreSQL auto-increment in sync after explicit IDs
        DB::statement(
            "SELECT setval(pg_get_serial_sequence('kolaborasi', 'id'), (SELECT COALESCE(MAX(id), 1) FROM kolaborasi))"
        );
    }
}
