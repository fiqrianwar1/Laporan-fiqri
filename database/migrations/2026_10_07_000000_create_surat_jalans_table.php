<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Surat jalan (barang keluar gudang).
     *
     * Satu baris = satu barang yang dikirim pada satu nomor surat. Barang
     * dalam satu surat dikelompokkan lewat nomor_surat + tanggal, jadi
     * kolomnya mengikuti form surat jalan di screenshot: nomor surat,
     * tanggal, tujuan, lalu daftar barangnya.
     */
    public function up(): void
    {
        Schema::create('surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('cabang_area')->default('Wira Toyota Banjarmasin');
            $table->date('tanggal');
            $table->string('nomor_surat');
            $table->unsignedSmallInteger('nomor_urut')->nullable();
            $table->string('kode_barang')->nullable();
            $table->string('nama_barang');
            $table->string('kemasan_barang')->nullable();
            $table->integer('jumlah')->default(0);
            $table->string('asal_penyimpanan')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();

            // Dipakai untuk mencari barang satu surat dengan cepat.
            $table->index(['nomor_surat', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalans');
    }
};
