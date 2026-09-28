<?php

namespace App\Support;

/**
 * Penampil nilai rupiah.
 *
 * Aturan yang dipakai di seluruh aplikasi:
 *  - Nilai TIDAK dibulatkan. Kalau hasil hitungannya ,50 atau ,60 tetap
 *    ditulis apa adanya, jadi angkanya cocok dengan faktur.
 *  - Selalu dua angka di belakang koma, dengan koma sebagai pemisah
 *    desimal dan titik sebagai pemisah ribuan (format Indonesia).
 *    Contoh: 416100.00 -> "416.100,00" dan 1037461.5 -> "1.037.461,50".
 *  - Total (per nota, per cabang, grand total) dijumlahkan dari nilai
 *    mentah, jadi tidak ada selisih akibat pembulatan.
 */
class Rupiah
{
    /**
     * Angka saja tanpa awalan "Rp" - dipakai kalau "Rp" ditulis terpisah.
     */
    public static function angka($nilai): string
    {
        return number_format((float) $nilai, 2, ',', '.');
    }

    /**
     * Lengkap dengan awalan "Rp ".
     */
    public static function format($nilai): string
    {
        return 'Rp '.static::angka($nilai);
    }

    /**
     * Sama seperti format(), tapi kalau nilainya nol cukup ditulis "Rp 0".
     * Dipakai untuk kolom diskon di laporan yang memang tidak pakai diskon,
     * supaya tidak terlihat penuh angka nol yang tidak perlu.
     */
    public static function formatRingkas($nilai): string
    {
        return (float) $nilai === 0.0 ? 'Rp 0' : static::format($nilai);
    }
}
