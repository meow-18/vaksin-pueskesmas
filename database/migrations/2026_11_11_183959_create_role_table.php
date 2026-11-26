<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role', function (Blueprint $table) {
            $table->unsignedTinyInteger('id_role', true);
            $table->string('nama_role', 30)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};