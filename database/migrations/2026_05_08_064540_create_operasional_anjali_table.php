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

            $table->foreignId('cabang_id')->constrained('cabang');

            $table->enum('hari', [1,2,3,4,5,6,7]);

            $table->time('waktu_buka');

            $table->time('waktu_tutup');

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
        Schema::dropIfExists('operasional_anjali');
    }
};
