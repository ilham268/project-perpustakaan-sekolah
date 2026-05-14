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
            $table->string('nama_kelas'); // contoh: X RPL 1
            $table->string('jurusan');    // contoh: RPL
            $table->timestamps();

            $table->unique('nama_kelas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};