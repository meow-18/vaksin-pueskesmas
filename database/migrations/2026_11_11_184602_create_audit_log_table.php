<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->unsignedBigInteger('id_audit_log', true);
            $table->unsignedInteger('id_pengguna')->nullable();
            $table->dateTime('waktu')->useCurrent();
            $table->string('aktivitas', 50);
            $table->string('objek', 50)->nullable();
            $table->unsignedBigInteger('id_objek')->nullable();
            $table->text('detail')->nullable();

            $table->index('waktu', 'idx_audit_waktu');
            $table->index('id_pengguna', 'idx_audit_pengguna');
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};