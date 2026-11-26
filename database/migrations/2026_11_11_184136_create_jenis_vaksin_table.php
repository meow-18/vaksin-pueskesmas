<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_vaksin', function (Blueprint $table) {
            $table->unsignedInteger('id_jenis_vaksin', true);
            $table->string('kode', 30)->unique();
            $table->string('nama_vaksin', 150);
            $table->string('nama_generik', 150)->nullable();
            $table->string('produsen', 150)->nullable();
            $table->string('bentuk_sediaan', 50)->nullable();
            $table->string('dosis_per_vial', 50)->nullable();
            $table->unsignedSmallInteger('vial_per_kotak');
            $table->enum('satuan', ['vial', 'dosis'])->default('vial');
            $table->string('suhu_simpan', 50)->nullable();
            $table->unsignedInteger('stok_minimum')->default(0);
            $table->boolean('aktif')->default(true);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE jenis_vaksin ADD CONSTRAINT ck_jenis_vial_per_kotak CHECK (vial_per_kotak > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_vaksin');
    }
};