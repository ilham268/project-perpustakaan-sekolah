<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kategori_pinjams', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 100);
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->string('kelas', 50)->nullable();
            $table->timestamps();

            $table->foreign('kelas_id')
                ->references('id')
                ->on('kelas')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kategori_pinjams');
    }
};