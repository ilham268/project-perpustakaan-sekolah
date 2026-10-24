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
            $table->string('kelas')->nullable()->change();
            $table->string('jurusan')->nullable()->change();
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Migration tidak dapat dibatalkan karena data guru membutuhkan field profil yang nullable.');
    }
};
