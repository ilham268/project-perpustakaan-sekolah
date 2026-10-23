<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nomor_identitas')->nullable()->change();
        });

        Schema::table('teacher_guest_books', function (Blueprint $table) {
            $table->string('metode_absensi')->default('manual')->after('keperluan');
        });
    }

    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::table('users')->whereNull('nomor_identitas')->exists()) {
            throw new RuntimeException('Masih ada user tanpa nomor identitas. Isi identitas sebelum rollback migrasi.');
        }

        Schema::table('teacher_guest_books', function (Blueprint $table) {
            $table->dropColumn('metode_absensi');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('nomor_identitas')->nullable(false)->change();
        });
    }
};
