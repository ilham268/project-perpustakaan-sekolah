<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pinjam_kelas', 'status_denda')) {
            Schema::table('pinjam_kelas', function (Blueprint $table) {
                $table->enum('status_denda', ['pending', 'paid'])
                    ->default('pending')
                    ->after('denda');
            });
        }

        if (!Schema::hasColumn('pinjam_kelas', 'tanggal_bayar_denda')) {
            Schema::table('pinjam_kelas', function (Blueprint $table) {
                $table->timestamp('tanggal_bayar_denda')
                    ->nullable()
                    ->after('status_denda');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pinjam_kelas', 'tanggal_bayar_denda')) {
            Schema::table('pinjam_kelas', function (Blueprint $table) {
                $table->dropColumn('tanggal_bayar_denda');
            });
        }

        if (Schema::hasColumn('pinjam_kelas', 'status_denda')) {
            Schema::table('pinjam_kelas', function (Blueprint $table) {
                $table->dropColumn('status_denda');
            });
        }
    }
};