<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->year('tahun_pengadaan')->nullable();
            $table->string('nomor_klasifikasi')->nullable();
            $table->string('judul');
            $table->text('synopsis')->nullable();
            $table->string('foto')->nullable();
            $table->string('penulis')->nullable();
            $table->string('penerbit')->nullable();
            $table->year('tahun')->nullable();
            $table->string('nomor_rak')->nullable();
            $table->string('sumber_buku')->nullable();
            $table->string('jenis_koleksi')->nullable();
            $table->unsignedInteger('jumlah_eksemplar')->default(0);

            $table->index('tahun_pengadaan');
            $table->index('jenis_koleksi');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};