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
        Schema::table('booking_pasien', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('status_pasien');
            $table->timestamp('finished_at')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_pasien', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'finished_at']);
        });
    }
};
