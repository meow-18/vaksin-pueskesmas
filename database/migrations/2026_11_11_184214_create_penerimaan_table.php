<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan', function (Blueprint $table) {
            $table->unsignedInteger('id_penerimaan', true);
            $table->unsignedInteger('id_pengguna');
            $table->date('tanggal_penerimaan');
            $table->string('sumber_penerimaan', 150);
            $table->string('no_dokumen_pengiriman', 100);
            $table->dateTime('created_at')->useCurrent();

            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan');
    }
};