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
        Schema::create('referral_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('booking')->nullOnDelete();
            $table->string('reward_type'); // e.g. half_session, full_session
            $table->integer('points_used');
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('booking', function (Blueprint $table) {
            $table->string('reward_referral_type')->nullable()->after('status');
            $table->integer('poin_referral_digunakan')->default(0)->after('reward_referral_type');
            $table->decimal('diskon_referral', 12, 2)->default(0)->after('poin_referral_digunakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->dropColumn(['reward_referral_type', 'poin_referral_digunakan', 'diskon_referral']);
        });

        Schema::dropIfExists('referral_redemptions');
    }
};
