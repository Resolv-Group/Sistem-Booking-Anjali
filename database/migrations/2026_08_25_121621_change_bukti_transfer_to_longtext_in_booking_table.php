<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix: TEXT column (65KB) is too small for base64-encoded images.
     * Change to LONGTEXT (4GB) to support compressed WebP image data.
     */
    public function up(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->longText('bukti_transfer_booking_path')->nullable()->change();
        });

        // Also ensure pasiens.foto is longText (may have been created as TEXT on some environments)
        Schema::table('pasiens', function (Blueprint $table) {
            $table->longText('foto')->nullable()->change();
        });

        // Ensure karyawans.foto is longText
        Schema::table('karyawans', function (Blueprint $table) {
            $table->longText('foto')->nullable()->change();
        });

        // Ensure kolaborasi.logo is longText
        Schema::table('kolaborasi', function (Blueprint $table) {
            $table->longText('logo')->nullable()->change();
        });

        // Ensure rekam_medis_fotos.foto is longText
        Schema::table('rekam_medis_fotos', function (Blueprint $table) {
            $table->longText('foto')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->text('bukti_transfer_booking_path')->nullable()->change();
        });
    }
};
