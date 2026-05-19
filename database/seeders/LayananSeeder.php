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
            'kolaborasi_id' => 1,
            'nama' => 'Akupunktur',
            'deskripsi' => 'Terapi akupunktur medis profesional',
            'base_harga' => 150000,
            'homecare_harga' => 150000,
            'status' => 'Tersedia',
            'created_by' => '1',
            'updated_by' => '1'
        ]);

        Layanan::create([
            'kolaborasi_id' => 1,
            'nama' => 'Bekam Medis',
            'deskripsi' => 'Terapi bekam steril dengan protokol medis',
            'base_harga' => 125000,
            'homecare_harga' => 150000,
            'status' => 'Tersedia',
            'created_by' => '1',
            'updated_by' => '1'
        ]);

        Layanan::create([
            'kolaborasi_id' => 1,
            'nama' => 'Refleksi',
            'deskripsi' => 'Terapi pijat refleksi kaki dan tubuh',
            'base_harga' => 100000,
            'homecare_harga' => 150000,
            'status' => 'Tersedia',
            'created_by' => '1',
            'updated_by' => '1'
        ]);

        Layanan::create([
            'kolaborasi_id' => 1,
            'nama' => 'Stimulator',
            'deskripsi' => 'Terapi menggunakan alat stimulator',
            'base_harga' => 100000,
            'homecare_harga' => 150000,
            'status' => 'Tersedia',
            'created_by' => '1',
            'updated_by' => '1'
        ]);

        Layanan::create([
            'kolaborasi_id' => 2,
            'nama' => 'Moksa',
            'deskripsi' => 'Terapi menggunakan moxa',
            'base_harga' => 125000,
            'homecare_harga' => 150000,
            'diskon_persentase' => 10,
            'status' => 'Tersedia',
            'created_by' => '1',
            'updated_by' => '1'
        ]);

        Layanan::create([
            'kolaborasi_id' => 2,
            'nama' => 'TDP',
            'deskripsi' => 'Terapi TDP',
            'base_harga' => 125000,
            'homecare_harga' => 150000,
            'diskon_persentase' => 10,
            'status' => 'Tersedia',
            'created_by' => '1',
            'updated_by' => '1'
        ]);

        Layanan::create([
            'kolaborasi_id' => 2,
            'nama' => 'Cupping',
            'deskripsi' => 'Terapi menggunakan alat cupping',
            'base_harga' => 125000,
            'homecare_harga' => 150000,
            'diskon_persentase' => 10,
            'status' => 'Tersedia',
            'created_by' => '1',
            'updated_by' => '1'
        ]);

        Layanan::create([
            'kolaborasi_id' => 2,
            'nama' => 'Sleeding Massage',
            'deskripsi' => 'Sleeding Massage',
            'base_harga' => 150000,
            'homecare_harga' => 150000,
            'diskon_persentase' => 10,
            'status' => 'Tersedia',
            'created_by' => '1',
            'updated_by' => '1'
        ]);

        Layanan::create([
            'kolaborasi_id' => 2,
            'nama' => 'Pijat Tradisional (Massage)',
            'deskripsi' => 'Pijat tradisional',
            'base_harga' => 150000,
            'homecare_harga' => 150000,
            'diskon_persentase' => 10,
            'status' => 'Tersedia',
            'created_by' => '1',
            'updated_by' => '1'
        ]);
    }
}
