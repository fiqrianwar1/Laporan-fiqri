<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Pemformat tanggal yang dipakai seragam di seluruh tampilan.
 *
 * Tanggal ditulis apa adanya ("1 September 2026") tanpa nama hari, supaya
 * kartu nota mudah dibaca sekilas dan konsisten di semua halaman.
 */
class Tanggal
{
    /**
     * Carbon::create()->month(9) melahirkan tanggal 1 September, jadi formatnya
     * "1 September 2026" - itulah gaya penulisan tanggal yang dipakai aplikasi.
     */
    public static function panjang(?Carbon $tanggal): string
    {
        return $tanggal
            ? $tanggal->copy()->locale('id')->translatedFormat('j F Y')
            : '-';
    }
}
