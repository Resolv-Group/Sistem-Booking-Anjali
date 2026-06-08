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
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('booking_pasien_id')
                ->constrained('booking_pasien')
                ->cascadeOnDelete();
                
            $table->string('tensi_sys')->nullable();
            $table->string('tensi_dia')->nullable();
            $table->string('tensi_pulse')->nullable();
            
            $table->text('area_tubuh')->nullable();
            $table->text('area_leher')->nullable();
            $table->text('area_dada')->nullable();
            $table->text('area_perut')->nullable();
            $table->text('area_tangan')->nullable();
            $table->text('area_kaki')->nullable();
            $table->text('area_punggung')->nullable();
            $table->text('area_pinggang')->nullable();
            
            $table->text('makan_suhu')->nullable(); // array
            $table->text('makan_rasa')->nullable(); // array
            $table->text('minum_suhu')->nullable(); // array
            $table->text('minum_tipe')->nullable(); // array
            
            $table->string('keringat')->nullable();
            $table->string('bab_kapan')->nullable();
            $table->string('bab_bentuk')->nullable();
            $table->string('bak_frekuensi')->nullable();
            $table->string('bak_warna')->nullable();
            
            $table->integer('skala_nyeri')->nullable();
            $table->string('tingkat_perbaikan')->nullable();
            
            $table->text('goal_terapi')->nullable();
            $table->text('saran_rekomendasi')->nullable();
            $table->text('catatan_khusus')->nullable();
            $table->text('catatan_terapis')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
