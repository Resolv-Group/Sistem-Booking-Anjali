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
        Schema::create('menu_terapi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daftar_layanan_id')->constrained('daftar_layanan');
            $table->foreignId('cabang_anjali_id')->constrained('cabang_anjali');

            $table->float('harga_menu');

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
        Schema::dropIfExists('menu_terapi');
    }
};
