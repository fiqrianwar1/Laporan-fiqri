<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Foto bukti surat jalan.
     *
     * Sama seperti foto nota di laporan oplosan & foto faktur di riwayat
     * order: satu kolom foto per baris barang. Karena barang dalam satu
     * surat sering di-upload foto yang isinya sama, penyaringan isi file
     * (FotoNota::unik) yang membuat satu foto hanya tampil sekali.
     */
    public function up(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->string('foto_print')->nullable()->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropColumn('foto_print');
        });
    }
};
