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
        Schema::create('kolaborasi', function (Blueprint $table) {
            $table->id();

            $table->string('nama_kolaborasi');
            $table->string('alamat_kolaborasi');
            $table->string('kota_kolaborasi');
            $table->string('no_telp_kolaborasi');
            $table->string('email_kolaborasi');

            $table->decimal('nilai_review', 3, 2)->nullable();
            $table->text('deskripsi_review')->nullable();

            $table->text('logo')->nullable();
            $table->string('logo_mime', 100)->nullable();

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
