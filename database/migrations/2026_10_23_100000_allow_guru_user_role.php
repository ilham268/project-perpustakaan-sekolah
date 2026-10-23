<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('siswa')->change();
        });
    }

    public function down(): void
    {
        if (DB::table('users')->where('role', 'guru')->exists()) {
            throw new RuntimeException('Role guru masih digunakan. Ubah user guru ke role lain sebelum rollback migrasi.');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'petugas', 'siswa'])->default('siswa')->change();
        });
    }
};
