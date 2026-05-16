<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE pinjam_kelas 
            MODIFY status ENUM('pending', 'disetujui', 'dikembalikan', 'denda') 
            NOT NULL DEFAULT 'pending'
        ");

        Schema::table('pinjam_kelas', function (Blueprint $table) {
            if (!Schema::hasColumn('pinjam_kelas', 'kondisi')) {
                $table->string('kondisi')->nullable()->after('status');
            }

            if (!Schema::hasColumn('pinjam_kelas', 'denda')) {
                $table->integer('denda')->default(0)->after('kondisi');
            }

            if (!Schema::hasColumn('pinjam_kelas', 'tanggal_denda')) {
                $table->timestamp('tanggal_denda')->nullable()->after('denda');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pinjam_kelas', function (Blueprint $table) {
            if (Schema::hasColumn('pinjam_kelas', 'tanggal_denda')) {
                $table->dropColumn('tanggal_denda');
            }

            if (Schema::hasColumn('pinjam_kelas', 'denda')) {
                $table->dropColumn('denda');
            }

            if (Schema::hasColumn('pinjam_kelas', 'kondisi')) {
                $table->dropColumn('kondisi');
            }
        });

        DB::statement("
            ALTER TABLE pinjam_kelas 
            MODIFY status ENUM('pending', 'disetujui', 'dikembalikan') 
            NOT NULL DEFAULT 'pending'
        ");
    }
};