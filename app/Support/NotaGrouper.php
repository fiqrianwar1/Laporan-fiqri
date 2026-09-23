<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Mengelompokkan baris transaksi (riwayat order / laporan oplosan) menjadi
 * satu "nota". Satu nota bisa berisi banyak item, jadi kuncinya adalah
 * nomor bukti + tanggal, bukan id barisnya.
 */
class NotaGrouper
{
    /**
     * @param  Collection  $items          baris transaksi yang sudah diurutkan
     * @param  callable    $nomorResolver  fn($item): string - nomor bukti/nota
     * @param  callable    $nomorAsli      fn($item): ?string - nomor bukti apa adanya
     * @return Collection  kumpulan nota siap dirender
     */
    public static function group(Collection $items, callable $nomorResolver, callable $nomorAsli): Collection
    {
        return $items
            ->groupBy(function ($item) use ($nomorResolver) {
                // Tanggal ikut jadi kunci supaya nota yang nomornya kebetulan
                // sama di bulan berbeda tetap tidak tercampur.
                $tanggal = $item->tanggal ? $item->tanggal->format('Y-m-d') : '-';

                return $tanggal . '|' . $nomorResolver($item);
            })
            ->map(function (Collection $baris) use ($nomorAsli) {
                $terurut = $baris
                    ->sortBy(fn($item) => $item->nomor_urut ?? PHP_INT_MAX)
                    ->values();

                return [
                    'kunci'        => $terurut->first()->tanggal?->format('Y-m-d')
                        . '|' . $nomorAsli($terurut->first()),
                    'tanggal'      => $terurut->first()->tanggal,
                    'nomor'        => $nomorAsli($terurut->first()),
                    'adaNomorUrut' => $terurut->contains(fn($item) => $item->nomor_urut !== null),
                    'items'        => $terurut,
                    'jumlahItem'   => $terurut->count(),
                ];
            })
            ->sortBy(fn($nota) => [$nota['tanggal']?->timestamp ?? 0, $nota['nomor']])
            ->values();
    }

    /**
     * Ringkasan nilai tiap nota memakai kolom penilai yang diminta.
     *
     * @param  Collection  $notas
     * @param  callable    $nilai  fn($item): float
     */
    public static function withTotals(Collection $notas, callable $nilai): Collection
    {
        return $notas->map(function ($nota) use ($nilai) {
            $nota['total'] = $nota['items']->sum($nilai);

            return $nota;
        });
    }
}
