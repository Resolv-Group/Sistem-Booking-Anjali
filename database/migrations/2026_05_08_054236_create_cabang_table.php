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
        Schema::create('cabang', function (Blueprint $table) {
            $table->id();

            $table->string('nama_cabang');
            $table->string('alamat_cabang');
            $table->string('no_telp_cabang');
            $table->string('email_cabang');

            $table->decimal('nilai_review', 3, 2)->nullable();
            $table->text('deskripsi_review')->nullable(); 

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
        Schema::dropIfExists('rumah_terapi');
    }
};
