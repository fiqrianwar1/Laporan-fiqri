<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Password akun contoh. Bisa diganti lewat .env (SEED_PASSWORD=...)
        // kalau tidak mau memakai kata sandi bawaan saat demo.
        $password = env('SEED_PASSWORD', 'password');

        // Akun untuk yang MENGISI laporan (tinter)
        User::updateOrCreate(
            ['email' => 'tinter@warnatanjungjaya.com'],
            [
                'name' => 'Tinter',
                'password' => Hash::make($password),
                'role' => 'tinter',
                'email_verified_at' => now(),
            ]
        );

        // Akun untuk ATASAN / manager yang hanya MELIHAT laporan (manajer)
        User::updateOrCreate(
            ['email' => 'manajer@warnatanjungjaya.com'],
            [
                'name' => 'Manajer',
                'password' => Hash::make($password),
                'role' => 'manajer',
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info("Akun siap dipakai (password: {$password}):");
        $this->command?->line('  tinter@warnatanjungjaya.com  - bisa mengisi laporan');
        $this->command?->line('  manajer@warnatanjungjaya.com - hanya melihat laporan');
    }
}
