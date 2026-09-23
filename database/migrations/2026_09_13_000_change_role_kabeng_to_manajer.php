<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah kolom role agar menerima nilai 'manajer' (sebelumnya 'kabeng').
     *
     * Dulu di sini ada perintah MySQL mentah (ALTER TABLE ... MODIFY) yang
     * hanya jalan di MySQL, sehingga migrasi gagal total saat test pakai
     * SQLite. Sekarang kolomnya ditulis ulang lewat Blueprint, jadi
     * jalan di MySQL maupun SQLite.
     */
    public function up(): void
    {
        // Pindahkan data lama dulu supaya tidak hilang.
        DB::table('users')->where('role', 'kabeng')->update(['role' => 'tinter']);

        $this->ubahKolomRole(['tinter', 'manajer']);
    }

    /**
     * Balikkan ke nilai semula (hanya kalau masih ada data 'manajer').
     */
    public function down(): void
    {
        DB::table('users')->where('role', 'manajer')->update(['role' => 'tinter']);

        $this->ubahKolomRole(['tinter', 'kabeng']);
    }

    /**
     * Tulis ulang kolom 'role' dengan daftar nilai yang diizinkan.
     *
     * @param  list<string>  $nilai
     */
    protected function ubahKolomRole(array $nilai): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // SQLite tidak bisa mengubah kolom langsung, jadi tabelnya dibuat
        // ulang lewat perubahan skema Laravel (legal di dalam migrasi).
        if ($driver === 'sqlite') {
            Schema::table('users', function (Blueprint $table) use ($nilai) {
                $table->enum('role', $nilai)->default('tinter')->change();
            });

            return;
        }

        $daftar = collect($nilai)->map(fn ($v) => "'{$v}'")->implode(', ');

        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM({$daftar}) NOT NULL DEFAULT 'tinter'");
    }
};
