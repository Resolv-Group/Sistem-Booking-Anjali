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
        Schema::create('terapis_operasional', function (Blueprint $table) {

            $table->id();

            $table->foreignId('terapis_id')
                ->constrained('karyawans')
                ->cascadeOnDelete();

            /*
            1 = Monday
            7 = Sunday
            */

            $table->enum('hari', [1,2,3,4,5,6,7]);

            $table->time('waktu_mulai')
                ->default('08:00:00');

            $table->integer('kuota')
                ->default(10);

            $table->enum('status',[
                'Aktif',
                'Tidak Aktif',
                'Libur'
            ])->default('Aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terapis_operasional');
    }
};
