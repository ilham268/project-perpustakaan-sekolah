<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value')->nullable();
            $table->timestamps();
        });

        DB::table('library_settings')->insert([
            [
                'key' => 'lama_pinjam_default',
                'value' => '7',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'denda_telat_per_hari',
                'value' => '10000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('library_settings');
    }
};