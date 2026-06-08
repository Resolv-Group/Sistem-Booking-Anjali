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
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');

        Schema::create('pasiens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->uuid('pasien_public_id')->default(DB::raw('gen_random_uuid()'))->unique();

            $table->string('nik', 16)->unique()->nullable();
            $table->string('nama_pasien');
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            
            $table->string('golongan_darah', 10)->nullable();
            $table->integer('tinggi_badan')->nullable();
            $table->integer('berat_badan')->nullable();

            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->unique();
            $table->string('email', 100)->unique()->nullable();

            $table->string('kode_referral')->unique()->nullable();
            $table->integer('poin_referral')->default(0);

            $table->text('foto')->nullable();
            $table->string('foto_mime', 100)->nullable();

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

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
