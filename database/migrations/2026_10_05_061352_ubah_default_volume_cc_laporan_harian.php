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
        // Volume 0 itu sah - dipakai untuk pemakaian cat sisa. Default-nya
        // dijadikan 0 supaya baris yang volumenya dikosongkan tidak error.
        Schema::table('laporan_harian_oplosans', function (Blueprint $table) {
            $table->integer('volume_cc')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_harian_oplosans', function (Blueprint $table) {
            $table->integer('volume_cc')->default(null)->change();
        });
    }
};
