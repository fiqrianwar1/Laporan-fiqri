<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Satu nota bisa berisi banyak item barang. Supaya item-item itu tetap
     * terbaca sebagai satu nota, tiap baris diberi nomor urut di dalam
     * nomor bukti/nota yang sama.
     */
    public function up(): void
    {
        Schema::table('riwayat_orders', function (Blueprint $table) {
            $table->unsignedSmallInteger('nomor_urut')->nullable()->after('no_bukti_faktur');
        });

        Schema::table('laporan_oplosans', function (Blueprint $table) {
            $table->unsignedSmallInteger('nomor_urut')->nullable()->after('no_bukti_nota');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_orders', function (Blueprint $table) {
            $table->dropColumn('nomor_urut');
        });

        Schema::table('laporan_oplosans', function (Blueprint $table) {
            $table->dropColumn('nomor_urut');
        });
    }
};
