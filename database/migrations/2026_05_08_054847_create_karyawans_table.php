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
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');
        }

        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            if (DB::getDriverName() === 'pgsql') {
                $table->uuid('kode_karyawan')->default(DB::raw('gen_random_uuid()'))->unique();
            } else {
                $table->uuid('kode_karyawan')->unique();
            }
            $table->string('nik', 16)->unique()->nullable();
            $table->string('nama_karyawan');

            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->unique();
            $table->string('email', 100)->unique()->nullable();

            $table->enum('peran', ['Terapis', 'Admin Kolaborasi', 'Admin Global']);

            $table->decimal('nilai_review', 3, 2)->nullable();
            $table->string('deskripsi_review')->nullable();

            $table->date('tanggal_bergabung')->nullable();
            $table->foreignId('kolaborasi_id')->constrained('kolaborasi')->nullOnDelete();
            $table->enum('status_karyawan', ['Aktif', 'Tidak Aktif', 'Resign', 'PHK'])->default('Aktif');

            $table->longText('foto')->nullable();
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
        Schema::dropIfExists('karyawans');
    }
};
