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

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'completed',
                'cancelled',
            ])->default('pending');

            $table->string('bukti_transfer_booking_path')
                ->nullable();

            $table->string('bukti_transfer_booking_mime')
                ->nullable();

            $table->text('alasan_status')
                ->nullable();

            $table->string('batalkan_type')
                ->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

            $table->foreignId('rejected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('rejected_at')
                ->nullable();

            $table->foreignId('completed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('completed_at')
                ->nullable();

            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('cancelled_at')
                ->nullable();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

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
