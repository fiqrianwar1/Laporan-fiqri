<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_orders', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('no_bukti_faktur');
            $table->string('kode_barang')->nullable();
            $table->string('nama_barang');
            $table->integer('qty');
            $table->string('satuan'); // ROL, GLN, PCS, LTR, TIN
            $table->decimal('harga_satuan', 14, 2);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->string('foto_faktur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_orders');
    }
};
