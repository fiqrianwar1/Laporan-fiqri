<?php

namespace App\Support;

/**
 * Logo perusahaan untuk kop dokumen PDF.
 *
 * DomPDF di konteks web menolak gambar data-URI base64 (PDF-nya jadi kosong),
 * tapi berhasil kalau diberi path file biasa. Karena itu helper ini
 * mengembalikan PATH file (bukan data-URI), dan view PDF memakai
 * src="{{ \App\Support\Logo::path() }}".
 *
 * File logo dicari berurutan: logo.jpg, logo.jpeg, logo.png, logo.webp,
 * logo.svg. Yang pertama ditemukan itulah yang dipakai.
 */
class Logo
{
    /**
     * Daftar nama file yang dicoba, berurutan dari yang paling diutamakan.
     *
     * logo.jpg dipakai lebih dulu karena itu logo resmi yang diunggah ke
     * public/images. JPEG juga aman digambar DomPDF. Sisanya (jpeg/png/webp/
     * svg) tinggal cadangan kalau sewaktu-waktu lambangnya diganti format.
     *
     * @var array<int, string>
     */
    protected const KANDIDAT = ['logo.jpg', 'logo.jpeg', 'logo.png', 'logo.webp', 'logo.svg'];

    /**
     * Path lengkap file logo yang dipakai view PDF, atau null kalau tidak ada.
     *
     * Dipakai sebagai src <img> oleh DomPDF. Pemisah path diseragamkan jadi
     * garis miring (/) supaya aman dibaca dompdf di Windows maupun Linux.
     */
    public static function path(): ?string
    {
        foreach (self::KANDIDAT as $nama) {
            $lengkap = public_path('images/'.$nama);

            if (is_file($lengkap)) {
                return str_replace('\\', '/', $lengkap);
            }
        }

        return null;
    }

    /**
     * Alias path() dengan nama lama, supaya view PDF yang masih memanggil
     * dataUri() tetap jalan tanpa perlu diubah.
     */
    public static function dataUri(): ?string
    {
        return self::path();
    }

    /**
     * true kalau file logo ada.
     */
    public static function ada(): bool
    {
        return self::path() !== null;
    }
}
