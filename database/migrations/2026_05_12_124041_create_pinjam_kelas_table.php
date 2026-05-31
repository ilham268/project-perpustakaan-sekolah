<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pinjam_kelas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kategori_pinjam_id')
                ->constrained('kategori_pinjams')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('book_id')
                ->nullable()
                ->constrained('books')
                ->nullOnDelete();

            $table->string('kode_buku')->nullable();

            $table->enum('status', [
                'pending',
                'disetujui',
                'dikembalikan',
                'denda',
            ])->default('pending');

            $table->enum('kondisi', [
                'baik',
                'rusak',
                'hilang',
            ])->nullable();

            $table->integer('denda')->default(0);

            $table->enum('status_denda', [
                'pending',
                'paid',
            ])->nullable()->default(null);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pinjam_kelas');
    }
};