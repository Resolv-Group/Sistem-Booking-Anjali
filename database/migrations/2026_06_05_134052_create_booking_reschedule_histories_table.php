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
        Schema::create('booking_reschedule_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained('booking')
                ->cascadeOnDelete();

            $table->foreignId('old_terapis_sesi_id')
                ->constrained('terapis_sesi')
                ->cascadeOnDelete();

            $table->foreignId('new_terapis_sesi_id')
                ->constrained('terapis_sesi')
                ->cascadeOnDelete();

            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('alasan_old')
                ->nullable();

            $table->text('alasan_new')
                ->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_reschedule_histories');
    }
};
