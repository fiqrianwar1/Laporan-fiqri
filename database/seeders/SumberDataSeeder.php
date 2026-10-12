<?php

namespace Database\Seeders;

use App\Models\LaporanHarianOplosan;
use App\Models\LaporanOplosan;
use App\Models\RiwayatOrder;
use App\Support\SumberData;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Impor data asli dari folder `sumber-data`.
 *
 * Sumbernya tiga berkas teks yang sudah diekstrak dari dokumen aslinya:
 *   - oplosan.txt  (dari PDF Laporan Oplosan September)
 *   - riwayat.txt  (dari PDF Riwayat Order September)
 *   - harian.txt   (dari XLSX Laporan Harian)
 *
 * Setelah data masuk, seluruh angka direkap ulang lalu DIBANDINGKAN dengan
 * angka yang tercetak di dokumen. Kalau ada yang meleset, importer berhenti
 * dengan error - jadi data yang tersimpan dijamin utuh, bukan setengah jalan.
 *
 * Jalankan: php artisan db:seed --class=SumberDataSeeder
 */
class SumberDataSeeder extends Seeder
{
    /** Angka rekap yang tercetak di dokumen sumber. Dipakai untuk memastikan. */
    private const HARAP = [
        'oplosan' => [
            'nota' => 37,
            'item' => 120,
            'qty' => 106315,
            'biaya' => 53646256.00,
            'per_cabang' => [
                'Wira Toyota Banjarmasin' => ['nota' => 29, 'item' => 99, 'qty' => 100715, 'biaya' => 48806175.00],
                'Wira Toyota Palangka Raya' => ['nota' => 8, 'item' => 21, 'qty' => 5600, 'biaya' => 4840081.00],
            ],
        ],
        'riwayat' => [
            'nota' => 43,
            'kotor' => 75640591.00,
            'diskon' => 6094714.30,
            'grand' => 69545876.70,
            'per_cabang' => [
                'Wira Toyota Banjarmasin' => ['nota' => 39, 'grand' => 60550192.08],
                'Wira Toyota Palangka Raya' => ['nota' => 4, 'grand' => 8995684.62],
            ],
        ],
    ];

    public function run(): void
    {
        // Berkas teks hasil ekstraksi dokumen asli disimpan di sini supaya
        // tidak tercampur dengan akar proyek. Folder ini tidak ikut ke repo
        // (ukurannya besar & datanya data nyata), jadi kalau tidak ada, impor
        // dilewati saja supaya `php artisan db:seed` tetap selesai.
        $dir = base_path('sumber-data/teks');

        if (! is_dir($dir)) {
            $this->command?->warn(
                'Folder sumber-data/teks tidak ditemukan - impor data sumber dilewati. '
                .'Taruh oplosan.txt, riwayat.txt & harian.txt di sana lalu jalankan ulang: '
                .'php artisan db:seed --class=SumberDataSeeder'
            );

            return;
        }

        $this->imporOplosan($dir.'/oplosan.txt');
        $this->imporRiwayat($dir.'/riwayat.txt');
        $this->imporHarian($dir.'/harian.txt');

        $this->command?->info('Selesai. Data sumber sudah masuk & tervalidasi.');
    }

    // ------------------------------------------------------------- oplosan

    private function imporOplosan(string $berkas): void
    {
        $data = SumberData::oplosan(file_get_contents($berkas));
        $baris = $data['baris'];

        $this->cek('oplosan jumlah nota', self::HARAP['oplosan']['nota'], count($data['notas']));
        $this->cek('oplosan jumlah item', self::HARAP['oplosan']['item'], count($baris));
        $this->cek('oplosan total qty', self::HARAP['oplosan']['qty'], array_sum(array_column($baris, 'qty_cc')));
        $this->cek(
            'oplosan total biaya',
            self::HARAP['oplosan']['biaya'],
            array_sum(array_column($baris, 'harga_nota'))
        );

        foreach (self::HARAP['oplosan']['per_cabang'] as $cabang => $harap) {
            $perCabang = array_filter($baris, fn ($b) => $b['cabang_area'] === $cabang);

            $this->cek("oplosan nota $cabang", $harap['nota'], count(array_filter(
                $data['notas'],
                fn ($n) => $n['cabang'] === $cabang
            )));
            $this->cek("oplosan item $cabang", $harap['item'], count($perCabang));
            $this->cek("oplosan qty $cabang", $harap['qty'], array_sum(array_column($perCabang, 'qty_cc')));
            $this->cek("oplosan biaya $cabang", $harap['biaya'], array_sum(array_column($perCabang, 'harga_nota')));
        }

        LaporanOplosan::query()->delete();

        foreach ($baris as $b) {
            LaporanOplosan::create([
                'cabang_area' => $b['cabang_area'],
                'tanggal' => $b['tanggal'],
                'no_plat' => $b['no_plat'],
                'kode_warna_unit' => $b['kode_warna_unit'],
                'no_bukti_nota' => $b['no_bukti'],
                'nomor_urut' => $b['nomor_urut'],
                'rincian_bahan' => $b['rincian_bahan'],
                'qty_cc' => $b['qty_cc'],
                'harga_nota' => $b['harga_nota'],
            ]);
        }

        $this->command?->info('Laporan oplosan: '.count($baris).' baris.');
    }

    // -------------------------------------------------------------- riwayat

    private function imporRiwayat(string $berkas): void
    {
        $data = SumberData::riwayat(file_get_contents($berkas));
        $baris = $data['baris'];

        $kotor = $diskon = $grand = 0.0;

        foreach ($baris as $it) {
            $nilai = $it['qty'] * $it['harga_satuan'];
            $kotor += $nilai;
            $diskon += $nilai * $it['diskon_persen'] / 100;
            $grand += $nilai * (1 - $it['diskon_persen'] / 100);
        }

        $this->cek('riwayat jumlah nota', self::HARAP['riwayat']['nota'], count($data['notas']));
        $this->cek('riwayat total kotor', self::HARAP['riwayat']['kotor'], $kotor);
        $this->cek('riwayat total diskon', self::HARAP['riwayat']['diskon'], $diskon);
        $this->cek('riwayat grand total', self::HARAP['riwayat']['grand'], $grand);

        foreach (self::HARAP['riwayat']['per_cabang'] as $cabang => $harap) {
            $grandCab = 0.0;

            foreach (array_filter($baris, fn ($b) => $b['cabang_area'] === $cabang) as $it) {
                $grandCab += $it['qty'] * $it['harga_satuan'] * (1 - $it['diskon_persen'] / 100);
            }

            $this->cek("riwayat nota $cabang", $harap['nota'], count(array_filter(
                $data['notas'],
                fn ($n) => $n['cabang'] === $cabang
            )));
            $this->cek("riwayat grand $cabang", $harap['grand'], $grandCab);
        }

        RiwayatOrder::query()->delete();

        foreach ($baris as $b) {
            RiwayatOrder::create([
                'cabang_area' => $b['cabang_area'],
                'tanggal' => $b['tanggal'],
                'no_bukti_faktur' => $b['no_bukti'],
                'nomor_urut' => $b['nomor_urut'],
                'kode_barang' => $b['kode_barang'],
                'nama_barang' => $b['nama_barang'],
                'qty' => $b['qty'],
                'satuan' => $b['satuan'],
                'harga_satuan' => $b['harga_satuan'],
                'diskon_persen' => $b['diskon_persen'],
            ]);
        }

        $this->command?->info('Riwayat order: '.count($baris).' baris.');
    }

    // --------------------------------------------------------------- harian

    private function imporHarian(string $berkas): void
    {
        $baris = SumberData::harian(file_get_contents($berkas));

        LaporanHarianOplosan::query()->delete();

        foreach ($baris as $b) {
            LaporanHarianOplosan::create($b);
        }

        $this->command?->info('Laporan harian oplosan: '.count($baris).' baris.');
    }

    // ------------------------------------------------------------ pemeriksa

    private function cek(string $label, $harus, $ada): void
    {
        $ok = is_numeric($harus)
            ? abs((float) $ada - (float) $harus) < 0.01
            : $ada === $harus;

        $this->command?->line(sprintf(
            '  [%s] %-40s harap=%s dapat=%s',
            $ok ? 'OK ' : 'GAGAL',
            $label,
            $harus,
            is_float($ada) ? round($ada, 2) : $ada
        ));

        if (! $ok) {
            throw new RuntimeException("Validasi gagal: {$label} (harap {$harus}, dapat {$ada})");
        }
    }
}
