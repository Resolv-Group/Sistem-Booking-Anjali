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
        Schema::create('operasional_rumah_terapi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kolaborasi_id')->constrained('kolaborasi');

            $table->enum('hari', [1,2,3,4,5,6,7]);

            $table->time('waktu_buka')->nullable();

            $table->time('waktu_tutup')->nullable();

            $table->enum('status_operasional', ['Buka', 'Tutup']);

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
        Schema::dropIfExists('operasional_rumah_terapi');
    }
};
