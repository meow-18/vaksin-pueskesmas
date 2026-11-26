<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->unsignedInteger('id_fasilitas', true);
            $table->string('nama_fasilitas', 150);
            $table->enum('jenis', ['Rumah Sakit', 'Klinik']);
            $table->string('alamat', 255);
            $table->string('kontak', 50)->nullable();
            $table->boolean('aktif')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas');
    }
};