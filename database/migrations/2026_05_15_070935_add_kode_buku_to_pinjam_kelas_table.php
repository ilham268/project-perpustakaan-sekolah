<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pinjam_kelas', function (Blueprint $table) {
            if (!Schema::hasColumn('pinjam_kelas', 'kode_buku')) {
                $table->string('kode_buku')->nullable()->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pinjam_kelas', function (Blueprint $table) {
            if (Schema::hasColumn('pinjam_kelas', 'kode_buku')) {
                $table->dropColumn('kode_buku');
            }
        });
    }
};