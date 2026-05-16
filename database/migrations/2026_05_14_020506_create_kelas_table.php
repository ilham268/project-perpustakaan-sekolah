<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->string('jurusan');
            $table->timestamps();

            // Yang unik adalah kombinasi kelas + jurusan
            $table->unique(['nama_kelas', 'jurusan'], 'kelas_nama_kelas_jurusan_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};