<?php

namespace App\Support;

/**
 * Daftar cabang/area yang dipakai di seluruh aplikasi.
 *
 * Dikumpulkan di satu tempat supaya dropdown filter, form input, dan
 * pengelompokan laporan selalu memakai nama yang sama persis.
 */
class Cabang
{
    /**
     * Nama cabang yang dikenal sistem.
     *
     * @return array<int, string>
     */
    public static function daftar(): array
    {
        return [
            'Wira Toyota Banjarmasin',
            'Wira Toyota Palangka Raya',
        ];
    }

    /**
     * Cabang default saat data baru dibuat.
     */
    public static function default(): string
    {
        return self::daftar()[0];
    }

    /**
     * Kunci pengelompokan yang tahan beda huruf besar/kecil & spasi.
     */
    public static function kunci(?string $nama): string
    {
        $bersih = preg_replace('/[^A-Za-z0-9]/', '', (string) $nama);

        return $bersih !== '' ? strtoupper($bersih) : 'TANPACABANG';
    }
}
