<?php

/**
 * Menyusun contoh PDF dari data dummy, tanpa menyentuh database.
 *
 * Dipakai buat mengecek tampilan kop, tabel, dan blok total setelah view
 * diubah: jalankan lalu buka file hasilnya di tab baru.
 *
 *   php scripts/render-contoh-pdf.php
 *
 * Hasilnya ditulis ke storage/app/contoh-pdf/.
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\Cabang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/** Satu baris laporan oplosan palsu. */
function itemOplosan(string $cabang, string $plat, string $warna, array $baris): Collection
{
    return collect($baris)->map(function ($b, $i) use ($cabang, $plat, $warna) {
        return (object) [
            'nomor_urut' => $i + 1,
            'no_plat' => $plat,
            'kode_warna_unit' => $warna,
            'rincian_bahan' => $b[0],
            'qty_cc' => $b[1],
            'harga_nota' => $b[2],
            'nominal_diskon' => $b[3],
            'cabang_area' => $cabang,
            'foto_nota' => null,
        ];
    });
}

$bjm = Cabang::daftar()[0];
$pal = Cabang::daftar()[1] ?? $bjm;

$notas = collect([
    [
        'nomor' => 'BKM-2026-0881',
        'tanggal' => Carbon::parse('2026-10-02'),
        'items' => itemOplosan($bjm, 'DA 1234 XY', '1G3 / HIJAU', [
            ['Base coat hijau metalik 200 cc + thinner 50 cc', 250, 480000, 0],
            ['Clear coat 150 cc', 150, 275000, 25000],
        ]),
    ],
    [
        'nomor' => null,
        'tanggal' => Carbon::parse('2026-10-04'),
        'items' => itemOplosan($bjm, 'DA 8899 AB', '070 / PUTIH', [
            ['Base coat putih 300 cc + hardener 60 cc, tipe Avanza', 360, 610000, 10000],
        ]),
    ],
    [
        'nomor' => 'BKM-2026-0930',
        'tanggal' => Carbon::parse('2026-10-05'),
        'items' => itemOplosan($pal, 'KH 5567 CD', '3R3 / MERAH', [
            ['Base coat merah 180 cc', 180, 355000, 0],
            ['Thinner 100 cc', 100, 90000, 0],
        ]),
    ],
]);

$notas = $notas->map(function ($n) {
    $n['total'] = $n['items']->sum(fn ($item) => (float) $item->harga_nota - (float) $item->nominal_diskon);

    return $n;
});

$bagian = Cabang::kelompokkanNota($notas);

$data = [
    'notas' => $notas,
    'bagian' => $bagian,
    'totalOplosan' => $notas->sum(fn ($n) => $n['items']->count()),
    'totalCc' => $notas->sum(fn ($n) => $n['items']->sum('qty_cc')),
    'totalBiaya' => $notas->sum('total'),
    'namaBulan' => 'Oktober',
    'tahun' => '2026',
    'cabang' => null,
];

$hasil = storage_path('app/contoh-pdf');
if (! is_dir($hasil)) {
    mkdir($hasil, 0777, true);
}

foreach (['laporan_oplosan.pdf' => 'contoh-laporan-oplosan.pdf'] as $view => $nama) {
    $pdf = Pdf::loadView($view, $data)->setPaper('a4', 'landscape');
    $pdf->setOption('isPhpEnabled', true);
    $pdf->save($hasil.'/'.$nama);

    echo $nama.' -> '.$hasil.'/'.$nama.PHP_EOL;
}
