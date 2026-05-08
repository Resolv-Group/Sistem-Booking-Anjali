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
        Schema::create('daftar_layanan', function (Blueprint $table) {
            $table->id();

            $table->string('nama_layanan');
            $table->text('detail_layanan')->nullable();
            $table->integer('durasi_menit');
            $table->enum('status_layanan', ['Tersedia', 'Tidak Tersedia']);

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
        Schema::dropIfExists('daftar_layanan');
    }
};
