<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota', function (Blueprint $table) {
            $table->unsignedInteger('id_nota', true);
            $table->unsignedInteger('id_distribusi')->unique('uq_nota_distribusi');
            $table->string('no_nota', 30)->unique('uq_nota_nomor');
            $table->dateTime('tanggal_terbit')->useCurrent();
            $table->string('file_pdf', 255)->nullable();

            $table->foreign('id_distribusi')->references('id_distribusi')->on('distribusi')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota');
    }
};