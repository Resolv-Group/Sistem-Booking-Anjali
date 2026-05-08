<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pasiens', function (Blueprint $table) {
            $table->id();

            $table->string('pasien_public_id', 25)->unique();

            $table->string('nik', 16)->unique()->nullable();
            $table->string('nama_pasien');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);

            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->unique();
            $table->string('email', 100)->unique()->nullable();

            $table->string('kode_referral')->unique()->nullable();
            $table->integer('poin_referral')->default(0);
            $table->enum('membership_tier', ['Bronze', 'Silver', 'Gold', 'Platinum'])->default('Bronze');

            $table->string('foto_path')->nullable();
            $table->string('foto_mime', 100)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
};
