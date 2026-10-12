<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom `total_baris` menyimpan nilai total SEBENARNYA yang tercetak di
 * faktur, karena tidak semua faktur bisa dihitung dengan qty x harga_satuan.
 *
 * Faktur Wira Toyota memakai qty sebagai volume cat (CC/LTR) dan kolom
 * TOTAL di faktur bukan hasil perkalian itu. Faktur PT. Sumber Tanjung Jaya
 * memakai qty sebagai jumlah barang, jadi totalnya tetap qty x harga.
 *
 * Kolom ini nullable, jadi faktur lama yang belum diisi tetap memakai
 * perhitungan qty x harga seperti sebelumnya - tidak ada data yang berubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_orders', function (Blueprint $table) {
            $table->decimal('total_baris', 16, 2)->nullable()->after('diskon_persen');
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_orders', function (Blueprint $table) {
            $table->dropColumn('total_baris');
        });
    }
};
