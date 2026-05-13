<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'admin',
            'nomor_identitas' => '1',
            'password' => bcrypt('11111111'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'peminjam',
            'nomor_identitas' => '11',
            'password' => bcrypt('11111111'),
            'role' => 'peminjam',
        ]);

        User::create([
            'name' => 'petugas',
            'nomor_identitas' => '111',
            'password' => bcrypt('11111111'),
            'role' => 'petugas',
        ]);
    }
}
