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
        // Akun untuk yang MENGISI laporan (tinter)
        User::updateOrCreate(
            ['email' => 'tinter@warnatanjungjaya.com'],
            [
                'name' => 'Tinter',
                'password' => 'password',
                'role' => 'tinter',
            ]
        );

        // Akun untuk ATASAN / manager yang hanya MELIHAT laporan (manajer)
        User::updateOrCreate(
            ['email' => 'manajer@warnatanjungjaya.com'],
            [
                'name' => 'Manajer',
                'password' => 'password',
                'role' => 'manajer',
            ]
        );
    }
}
