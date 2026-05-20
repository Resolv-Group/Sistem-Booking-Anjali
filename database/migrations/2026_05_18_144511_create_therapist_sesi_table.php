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
        Schema::create('terapis_sesi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('terapis_id')->constrained('karyawans')->cascadeOnDelete();

            $table->foreignId('operasional_id')->constrained('terapis_operasional')->cascadeOnDelete();

            $table->foreignId('kolaborasi_id')->constrained('kolaborasi')->cascadeOnDelete();

            $table->date('tanggal_sesi');

            $table->time('waktu_mulai');

            $table->integer('kuota')->default(10);

            $table->enum('status', ['terbuka', 'tutup', 'selesai', 'dibatalkan'])->default('terbuka');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapist_sesi');
    }
};
