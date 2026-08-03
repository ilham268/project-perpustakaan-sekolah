<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_items', function (Blueprint $table) {
            $table->dropUnique('book_items_kode_buku_unique');
            $table->unique(['book_id', 'kode_buku']);
        });
    }

    public function down(): void
    {
        Schema::table('book_items', function (Blueprint $table) {
            $table->dropUnique(['book_id', 'kode_buku']);
            $table->unique('kode_buku');
        });
    }
};