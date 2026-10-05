<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laporan harian oplosan cat (paint mixing / tinter).
     *
     * Berbeda dengan tabel `laporan_oplosans`, di sini tiap baris berdiri
     * sendiri (tidak dikelompokkan per nota). Kolomnya mengikuti form harian
     * yang dipakai tim body & paint: satu baris = satu pekerjaan oplosan.
     */
    public function up(): void
    {
        Schema::create('laporan_harian_oplosans', function (Blueprint $table) {
            $table->id();
            $table->string('cabang_area')->default('Wira Toyota Banjarmasin');
            $table->date('tanggal');
            $table->string('plat_nomor');
            $table->string('kode_warna');
            $table->string('tipe_mobil');
            $table->string('bahan_cat');
            $table->integer('volume_cc')->default(0);
            $table->time('jam_dibuat')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->unsignedSmallInteger('durasi_menit')->nullable();
            $table->string('hasil_matching')->default('Sama');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_harian_oplosans');
    }
};
