<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Layanan::create([
            'nama' => 'Akupunktur',
            'deskripsi' => 'Terapi akupunktur medis profesional',
            'base_harga' => 150000,
            'homecare_harga' => 200000,
            'status' => 'Tersedia',
        ]);

        Layanan::create([
            'nama' => 'Bekam Medis',
            'deskripsi' => 'Terapi bekam steril dengan protokol medis',
            'base_harga' => 125000,
            'homecare_harga' => 175000,
            'status' => 'Tersedia',
        ]);

        Layanan::create([
            'nama' => 'Refleksi',
            'deskripsi' => 'Terapi pijat refleksi kaki dan tubuh',
            'base_harga' => 100000,
            'homecare_harga' => 150000,
            'status' => 'Tersedia',
        ]);
    }
}
