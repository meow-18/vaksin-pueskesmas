<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribusi', function (Blueprint $table) {
            $table->unsignedInteger('id_distribusi', true);
            $table->unsignedInteger('id_fasilitas');
            $table->unsignedInteger('id_pengguna');
            $table->dateTime('tanggal_transaksi')->useCurrent();
            $table->string('nama_pengambil', 100);
            $table->enum('status', ['Draft', 'Final', 'Dibatalkan'])->default('Draft');
            $table->string('alasan_batal', 255)->nullable();
            $table->dateTime('waktu_batal')->nullable();
            $table->unsignedInteger('id_pengguna_pembatal')->nullable();

            $table->index('tanggal_transaksi', 'idx_distribusi_tanggal');
            $table->index('status', 'idx_distribusi_status');
            $table->foreign('id_fasilitas')->references('id_fasilitas')->on('fasilitas')->restrictOnDelete();
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->restrictOnDelete();
            $table->foreign('id_pengguna_pembatal')->references('id_pengguna')->on('pengguna')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribusi');
    }
};