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
        Schema::create('terapis_layanan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('terapis_id')
                ->constrained('karyawans')
                ->cascadeOnDelete();

            $table->foreignId('layanan_id')
                ->constrained('layanan')
                ->cascadeOnDelete();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapist_service');
    }
};
