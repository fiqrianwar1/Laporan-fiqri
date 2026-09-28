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

    /**
     * Singkatan cabang untuk kode nomor dokumen, mis.
     * "Wira Toyota Banjarmasin" -> "BJM".
     *
     * Kata yang sama di semua cabang dibuang dulu ("Wira", "Toyota"), baru
     * diambil tiga huruf pertama dari kata yang tersisa. Jadi Banjarmasin jadi
     * BJM dan Palangka Raya jadi PAL - kalau memakai kata terakhir apa adanya,
     * Palangka Raya malah jadi "RAY" yang tidak mewakili nama cabangnya.
     */
    public static function singkatan(?string $nama): string
    {
        $kata = preg_split('/[^A-Za-z0-9]+/', (string) $nama, -1, PREG_SPLIT_NO_EMPTY);

        if (! $kata) {
            return 'CBG';
        }

        // Kata yang muncul di semua nama cabang tidak bisa dipakai untuk
        // membedakan, jadi disaring dulu.
        $umum = array_map('strtolower', self::kataUmum());
        $khas = array_values(array_filter(
            $kata,
            fn ($k) => ! in_array(strtolower($k), $umum, true)
        ));

        $dipakai = $khas ?: $kata;

        return strtoupper(substr((string) reset($dipakai), 0, 3));
    }

    /**
     * Kata yang selalu ada di nama cabang sehingga tidak membedakan apa pun.
     *
     * @return array<int, string>
     */
    protected static function kataUmum(): array
    {
        return ['wira', 'toyota', 'dealer', 'auto', '2000'];
    }

    /**
     * Pisahkan nota menjadi kelompok per cabang, lengkap dengan nomor urut
     * yang dimulai ulang dari 1 di tiap cabang.
     *
     * Dipakai halaman PDF supaya nota Banjarmasin & Palangka tidak tercampur
     * dalam satu deretan panjang, tapi tetap bisa dihitung totalnya bersama.
     *
     * @param  \Illuminate\Support\Collection  $notas
     * @return \Illuminate\Support\Collection
     */
    public static function kelompokkanNota($notas): \Illuminate\Support\Collection
    {
        // Urutan cabang mengikuti daftar resmi, bukan urutan data, supaya
        // Banjarmasin selalu muncul lebih dulu dan hasil cetak "semua cabang"
        // tetap konsisten tiap kali dibuka.
        return $notas
            ->groupBy(fn ($nota) => $nota['items']->first()->cabang_area ?: self::default())
            ->sortBy(function ($baris, $nama) {
                $posisi = array_search($nama, self::daftar(), true);

                return $posisi === false ? PHP_INT_MAX : $posisi;
            })
            ->map(function ($baris, $nama) {
                return [
                    'cabang' => $nama,
                    'kode'   => self::singkatan($nama),
                    'notas'  => $baris->values()->map(function ($nota, $i) {
                        // Nomor urut bagian ini yang ditampilkan di PDF
                        // ("Nota #1", "Nota #2", ...) dan mulai lagi dari 1
                        // begitu masuk cabang berikutnya.
                        $nota['nomorBagian'] = $i + 1;

                        return $nota;
                    }),
                ];
            })
            ->values();
    }
}
