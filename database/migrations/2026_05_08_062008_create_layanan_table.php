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
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kolaborasi_id')
                ->constrained('kolaborasi')
                ->cascadeOnDelete();

            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->decimal('base_harga', 15, 2)->nullable();
            $table->decimal('homecare_harga', 15, 2)->nullable();
            $table->decimal('diskon_persentase', 5, 2)->default(0);
            $table->enum('status', ['Tersedia', 'Tidak Tersedia']);

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
