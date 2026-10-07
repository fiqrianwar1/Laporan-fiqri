<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Mengelompokkan baris laporan harian oplosan menjadi "satu hari".
 *
 * Bedanya dengan NotaGrouper: laporan oplosan dikelompokkan per nomor nota,
 * sedangkan laporan harian cukup per tanggal. Jadi semua pekerjaan yang
 * tanggalnya sama (tanggal 1, tanggal 2, dst) tampil dalam satu blok - persis
 * rekap harian yang ditulis tim body & paint.
 */
class HarianGrouper
{
    /**
     * @param  Collection  $items  baris laporan harian yang sudah diurutkan
     * @return Collection kumpulan hari siap dirender
     */
    public static function group(Collection $items): Collection
    {
        return $items
            ->groupBy(fn ($item) => $item->tanggal ? $item->tanggal->format('Y-m-d') : '-')
            ->map(function (Collection $baris) {
                // Urutkan per jam kerja supaya kronologisnya jelas: pekerjaan
                // pagi tampil di atas, yang sore di bawah. Kalau jamnya kosong,
                // tenggelam ke bawah dan baru diurutkan lewat id.
                $terurut = $baris
                    ->sortBy([
                        fn ($a, $b) => strcmp((string) $a->jam_dibuat, (string) $b->jam_dibuat),
                        fn ($a, $b) => $a->getKey() <=> $b->getKey(),
                    ])
                    ->values();

                return [
                    'kunci' => $terurut->first()->tanggal?->format('Y-m-d') ?? '-',
                    'tanggal' => $terurut->first()->tanggal,
                    'items' => $terurut,
                    'jumlahItem' => $terurut->count(),
                    'volume' => $terurut->sum('volume_cc'),
                    'durasi' => $terurut->sum(fn ($b) => (int) $b->durasi_menit),
                    'sama' => $terurut->where('hasil_matching', 'Sama')->count(),
                ];
            })
            ->sortBy(fn ($hari) => $hari['tanggal']?->timestamp ?? 0)
            ->values();
    }
}
