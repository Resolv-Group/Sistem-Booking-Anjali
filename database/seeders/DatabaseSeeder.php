<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KolaborasiSeeder::class,
            LayananSeeder::class,
            OperasionalSeeder::class,
            UserSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
