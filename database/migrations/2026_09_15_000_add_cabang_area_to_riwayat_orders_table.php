<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat order juga perlu ditandai cabang/area supaya laporan belanja
     * bisa dipisah per cabang - sama seperti kolom di tabel laporan oplosan.
     */
    public function up(): void
    {
        Schema::table('riwayat_orders', function (Blueprint $table) {
            $table->string('cabang_area')
                ->default('Wira Toyota Banjarmasin')
                ->after('tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_orders', function (Blueprint $table) {
            $table->dropColumn('cabang_area');
        });
    }
};
