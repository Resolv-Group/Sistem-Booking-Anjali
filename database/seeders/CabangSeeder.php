<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('cabang')->insert([
            [
                'id' => 1,
                'nama_cabang' => 'Rumah Terapi Anjali',
                'alamat_cabang' => 'Alamat Rumah Terapi Anjali',
                'no_telp_cabang' => '08111111111',
                'email_cabang' => 'anjali@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'nama_cabang' => 'Lima Jari',
                'alamat_cabang' => 'Alamat Lima Jari',
                'no_telp_cabang' => '08222222222',
                'email_cabang' => 'limajari@example.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
