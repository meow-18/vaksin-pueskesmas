<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_distribusi', function (Blueprint $table) {
            $table->unsignedInteger('id_detail_distribusi', true);
            $table->unsignedInteger('id_distribusi');
            $table->unsignedInteger('id_batch');
            $table->unsignedInteger('jumlah');
            $table->boolean('batch_terverifikasi')->default(false);
            $table->dateTime('waktu_terverifikasi')->nullable();

            $table->unique(['id_distribusi', 'id_batch'], 'uq_detail_distribusi_batch');
            $table->foreign('id_distribusi')->references('id_distribusi')->on('distribusi')->restrictOnDelete();
            $table->foreign('id_batch')->references('id_batch')->on('batch')->restrictOnDelete();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE detail_distribusi ADD CONSTRAINT ck_detail_jumlah CHECK (jumlah > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_distribusi');
    }
};