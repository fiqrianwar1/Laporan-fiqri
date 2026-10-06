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

// ---------- Contoh laporan harian (rekap per pekerjaan, bukan per nota) ----------
$harian = collect([
    ['2026-10-02', 'DA 1424 PF', 'Black Doff', 'Avanza', 'Wanda SB', 250, '08:15', '09:00', 'Sama'],
    ['2026-10-02', 'F 1361 FBZ', '3Q3', 'Innova', 'Autobase', 320, '09:40', '10:35', 'Sama'],
    ['2026-10-03', 'D 1524 AMQ', 'Jet Black', 'Rush', 'Wanda 2K', 180, '14:10', '15:05', 'Mirip'],
    ['2026-10-03', 'DA 1818 LO', '1G3', 'Calya', 'Autocryl', 140, '15:30', '16:10', 'Sama'],
])->map(function ($b, $i) use ($bjm, $pal) {
    $durasi = App\Models\LaporanHarianOplosan::hitungDurasi($b[6], $b[7]);

    return (object) [
        'tanggal' => Carbon::parse($b[0]),
        'plat_nomor' => $b[1],
        'kode_warna' => $b[2],
        'tipe_mobil' => $b[3],
        'bahan_cat' => $b[4],
        'volume_cc' => $b[5],
        'jam_dibuat' => $b[6],
        'jam_selesai' => $b[7],
        'durasi_menit' => $durasi,
        'hasil_matching' => $b[8],
        'keterangan' => null,
        'cabang_area' => $i < 2 ? $bjm : $pal,
    ];
});

$dataHarian = [
    'baris' => $harian,
    'totalBaris' => $harian->count(),
    'totalVolume' => $harian->sum('volume_cc'),
    'totalDurasi' => $harian->sum('durasi_menit'),
    'jumlahSama' => $harian->where('hasil_matching', 'Sama')->count(),
    'namaBulan' => 'Oktober',
    'tahun' => '2026',
    'cabang' => null,
];

$namaHarian = 'contoh-laporan-harian-oplosan.pdf';
$pdfHarian = Pdf::loadView('laporan_harian.pdf', $dataHarian)->setPaper('a4', 'landscape');
$pdfHarian->setOption('isPhpEnabled', true);
$pdfHarian->save($hasil.'/'.$namaHarian);

echo $namaHarian.' -> '.$hasil.'/'.$namaHarian.PHP_EOL;
