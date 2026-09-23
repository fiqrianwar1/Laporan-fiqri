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
        Schema::create('laporan_oplosans', function (Blueprint $table) {
            $table->id();
            $table->string('cabang_area')->default('Wira Toyota Banjarmasin');
            $table->date('tanggal');
            $table->string('no_plat');
            $table->string('kode_warna_unit');
            $table->string('no_bukti_nota');
            $table->string('rincian_bahan');
            $table->integer('qty_cc');
            $table->decimal('harga_nota', 14, 2)->nullable();
            $table->string('foto_nota')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_oplosans');
    }
};
