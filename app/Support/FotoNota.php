<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Mengumpulkan foto bukti yang menempel pada satu nota.
 *
 * Satu nota sering diisi beberapa item, dan tiap item di-upload foto yang
 * isinya sama (hasil scan nota yang sama). Kalau tidak disaring, satu nota
 * bisa menampilkan 8 thumbnail identik.
 *
 * Karena itu penyaringan dilakukan berdasarkan ISI file (hash), bukan nama
 * path-nya: file yang isinya sama dianggap satu foto walaupun tersimpan
 * dengan nama berbeda.
 */
class FotoNota
{
    /**
     * Foto unik milik satu nota.
     *
     * @param  Collection  $items  item-item dalam satu nota
     * @param  string  $kolom  nama kolom foto (foto_nota / foto_faktur)
     * @param  int|null  $batas  jumlah foto maksimal yang ditampilkan
     * @return Collection kumpulan path foto yang sudah unik
     */
    public static function unik(Collection $items, string $kolom, ?int $batas = null): Collection
    {
        $sudahAda = [];
        $hasil = collect();

        foreach ($items as $item) {
            $path = $item->{$kolom} ?? null;

            if (blank($path)) {
                continue;
            }

            // Kalau file-nya sudah hilang dari storage, tetap ditampilkan
            // supaya kelihatan ada foto yang bermasalah, bukan malah disembunyikan.
            $cap = self::cap($path);

            if (isset($sudahAda[$cap])) {
                continue;
            }

            $sudahAda[$cap] = true;
            $hasil->push($path);

            if ($batas !== null && $hasil->count() >= $batas) {
                break;
            }
        }

        return $hasil;
    }

    /**
     * Cap isi file. Dihitung sekali per file per request.
     */
    protected static function cap(string $path): string
    {
        static $cache = [];

        if (isset($cache[$path])) {
            return $cache[$path];
        }

        $lengkap = Storage::disk('public')->path($path);

        return $cache[$path] = is_file($lengkap)
            ? 'hash:'.md5_file($lengkap)
            : 'path:'.$path;
    }
}
