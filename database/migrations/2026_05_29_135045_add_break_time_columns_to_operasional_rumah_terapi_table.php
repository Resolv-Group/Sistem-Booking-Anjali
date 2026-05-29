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
        Schema::table('operasional_rumah_terapi', function (Blueprint $table) {
            $table->time('waktu_istirahat_mulai')->nullable()->after('waktu_tutup');
            $table->time('waktu_istirahat_selesai')->nullable()->after('waktu_istirahat_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operasional_rumah_terapi', function (Blueprint $table) {
            $table->dropColumn(['waktu_istirahat_mulai', 'waktu_istirahat_selesai']);
        });
    }
};
