<?php

// Pemeriksa cepat: membuktikan format rupiah tidak membulatkan nilai.
// Jalankan: php scripts/cek-rupiah.php

require __DIR__.'/../vendor/autoload.php';

use App\Support\Rupiah;

$kasus = [
    ['harga satuan bulat', 105000.0, 'Rp 105.000,00'],
    ['hasil hitung ,50', 1037461.5, 'Rp 1.037.461,50'],
    ['hasil hitung ,60', 2995734.6, 'Rp 2.995.734,60'],
    ['hasil hitung ,10', 1596968.10, 'Rp 1.596.968,10'],
    ['hasil hitung ,48', 1545972.48, 'Rp 1.545.972,48'],
    ['nol ditulis singkat', 0.0, 'Rp 0'],
];

$gagal = 0;

foreach ($kasus as [$nama, $nilai, $harus]) {
    $dapat = $nama === 'nol ditulis singkat'
        ? Rupiah::formatRingkas($nilai)
        : Rupiah::format($nilai);

    $status = $dapat === $harus ? 'OK  ' : 'GAGAL';
    if ($dapat !== $harus) {
        $gagal++;
    }

    echo "$status $nama: '$dapat' (harusnya '$harus')".PHP_EOL;
}

// Total harus dijumlahkan dari nilai mentah, bukan dari angka yang
// sudah dibulatkan - kalau tidak, totalnya bisa selisih beberapa rupiah.
$nilaiMentah = [1545972.48, 1037461.50, 731767.50, 2995734.60];
$total = array_sum($nilaiMentah);
$totalBulat = array_sum(array_map(fn ($n) => round($n), $nilaiMentah));

echo PHP_EOL.'Total dari nilai mentah : '.Rupiah::format($total).PHP_EOL;
echo 'Total kalau dibulatkan   : '.Rupiah::format($totalBulat).PHP_EOL;
echo 'Selisih                  : '.Rupiah::format($total - $totalBulat).PHP_EOL;

echo PHP_EOL.($gagal === 0 ? 'Semua format benar.' : "$gagal kasus gagal.").PHP_EOL;

exit($gagal === 0 ? 0 : 1);
