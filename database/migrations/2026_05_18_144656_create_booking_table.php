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
        Schema::create('booking', function (Blueprint $table) {

            $table->id();

            $table->foreignId('booking_oleh_pasien_id')
                ->constrained('pasiens')
                ->cascadeOnDelete();

            $table->foreignId('terapis_sesi_id')
                ->constrained('terapis_sesi')
                ->cascadeOnDelete();

            $table->foreignId('layanan_id')
                ->constrained('layanan')
                ->cascadeOnDelete();

            $table->enum('status',[
                'pending',
                'approved',
                'rejected',
                'completed',
                'cancelled'
            ])->default('pending');

            $table->string('bukti_transfer_booking_path')
                ->nullable();
            
            $table->string('bukti_transfer_booking_mime')
                ->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
