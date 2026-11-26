<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch', function (Blueprint $table) {
            $table->unsignedInteger('id_batch', true);
            $table->unsignedInteger('id_jenis_vaksin');
            $table->unsignedInteger('id_penerimaan');
            $table->string('nomor_batch', 50);
            $table->date('tanggal_kadaluwarsa');
            $table->unsignedInteger('jumlah_diterima');
            $table->unsignedInteger('stok_saat_ini');
            $table->enum('status', ['Tersedia', 'Terbatas', 'Habis', 'Kadaluwarsa'])->default('Tersedia');

            $table->unique(['id_jenis_vaksin', 'nomor_batch'], 'uq_batch_jenis_nomor');
            $table->index(['id_jenis_vaksin', 'tanggal_kadaluwarsa'], 'idx_batch_fefo');
            $table->foreign('id_jenis_vaksin')->references('id_jenis_vaksin')->on('jenis_vaksin')->restrictOnDelete();
            $table->foreign('id_penerimaan')->references('id_penerimaan')->on('penerimaan')->restrictOnDelete();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE batch ADD CONSTRAINT ck_batch_jumlah CHECK (jumlah_diterima > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('batch');
    }
};