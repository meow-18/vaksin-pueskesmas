<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyesuaian_stok', function (Blueprint $table) {
            $table->unsignedInteger('id_penyesuaian_stok', true);
            $table->unsignedInteger('id_batch');
            $table->unsignedInteger('id_pengguna');
            $table->dateTime('tanggal')->useCurrent();
            $table->unsignedInteger('stok_sebelum');
            $table->unsignedInteger('stok_sesudah');
            $table->string('alasan', 255);

            $table->index('id_batch', 'idx_penyesuaian_batch');
            $table->foreign('id_batch')->references('id_batch')->on('batch')->restrictOnDelete();
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyesuaian_stok');
    }
};