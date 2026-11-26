<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->unsignedInteger('id_pengguna', true);
            $table->unsignedTinyInteger('id_role');
            $table->string('nama_pengguna', 100);
            $table->string('username', 50)->unique();
            $table->string('password_hash', 255);
            $table->boolean('aktif')->default(true);
            $table->dateTime('created_at')->useCurrent();

            $table->foreign('id_role')->references('id_role')->on('role')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};