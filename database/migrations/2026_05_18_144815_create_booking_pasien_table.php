<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_pasien', function (Blueprint $table) {

            $table->id();

            $table->foreignId('booking_id')
                ->constrained('booking')
                ->cascadeOnDelete();

            $table->foreignId('pasien_id')
                ->constrained('pasiens')
                ->cascadeOnDelete();

            $table->foreignId('layanan_id')->nullable()->constrained('layanan')->cascadeOnDelete();

            $table->text('keluhan_pasien')
                ->nullable();

            $table->text('catatan_terapis')
                ->nullable();

            $table->text('ringkasan_sesi')
                ->nullable();

            $table->enum('status_pasien',[
                'menunggu',
                'sedang berjalan',
                'selesai',
                'dibatalkan'
            ])->default('menunggu');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_pasien');
    }
};
