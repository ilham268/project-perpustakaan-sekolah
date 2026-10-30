<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pinjam_kelas', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Migration status peminjaman tidak dapat dibatalkan karena hal itu dapat menghapus riwayat penolakan.');
    }
};
