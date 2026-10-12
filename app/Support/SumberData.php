<?php

namespace App\Support;

use RuntimeException;

/**
 * Pembaca berkas sumber mentah di folder `sumber-data`.
 *
 * Berkas mentah itu adalah hasil teks dari dokumen aslinya:
 *  - `oplosan.txt` — teks dari PDF Laporan Oplosan (kolom dipisah TAB)
 *  - `riwayat.txt` — teks dari PDF Riwayat Order (kolom dipisah TAB)
 *  - `harian.txt`  — teks dari XLSX Laporan Harian (kolom dipisah TAB)
 *
 * Kelas ini mengubah teks itu jadi baris-baris siap simpan. Semua
 * perhitungan uang memakai angka mentah supaya totalnya tidak meleset.
 */
class SumberData
{
    public const BULAN_ID = [
        'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
        'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
        'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12,
    ];

    /**
     * "Rp 53.646.256,00" -> 53646256.0 ; "1.450" -> 1450.0 ; "327" -> 327.0
     */
    public static function uangKeFloat(string $s): float
    {
        $s = preg_replace('/[^0-9.,-]/', '', trim($s));
        $s = str_replace('.', '', $s);
        $s = str_replace(',', '.', $s);

        return (float) $s;
    }

    /**
     * "1,200" (qty dokumen) -> 1200 ; "1.200" -> 1200.
     */
    public static function qtyKeInt(string $s): int
    {
        return (int) str_replace([',', '.'], '', trim($s));
    }

    /**
     * "1 September 2026" -> "2026-09-01".
     */
    public static function tanggalIndoKeIso(string $teks): string
    {
        if (! preg_match('/^(\d{1,2})\s+(\S+)\s+(\d{4})$/', trim($teks), $m)) {
            throw new RuntimeException("Tanggal tak dikenal: {$teks}");
        }

        $bulan = self::BULAN_ID[$m[2]] ?? null; // konstanta kelas, bukan method

        if (! $bulan) {
            throw new RuntimeException("Bulan tak dikenal: {$m[2]}");
        }

        return sprintf('%04d-%02d-%02d', (int) $m[3], $bulan, (int) $m[1]);
    }

    /**
     * Ubah serial tanggal Excel (mis. 46296) jadi "2026-09-02".
     *
     * Excel menghitung hari sejak 1899-12-30, jadi cukup ditambahkan ke
     * angka itu. Dipakai untuk isi `harian.txt` yang tanggalnya masih
     * berupa angka mentah dari sel Excel.
     */
    public static function serialExcelKeIso($nilai): string
    {
        $hari = (int) floor((float) $nilai);

        return date('Y-m-d', mktime(0, 0, 0, 1, $hari - 25568, 1970));
    }

    /**
     * Ubah pecahan hari Excel (mis. 0.3958333) jadi "HH:MM", atau terima
     * teks "09:30" apa adanya.
     */
    public static function jamExcelKeHi(?string $nilai): ?string
    {
        if ($nilai === null || trim($nilai) === '') {
            return null;
        }

        $nilai = trim($nilai);

        if (preg_match('/^\d{1,2}[:.]\d{2}$/', $nilai)) {
            [$j, $m] = preg_split('/[:.]/', $nilai);

            return sprintf('%02d:%02d', (int) $j, (int) $m);
        }

        $pecahan = (float) $nilai;
        $menitTotal = (int) round($pecahan * 24 * 60) % (24 * 60);

        return sprintf('%02d:%02d', intdiv($menitTotal, 60), $menitTotal % 60);
    }

    // ------------------------------------------------------------- oplosan

    /**
     * Satu baris item oplosan, kolom dipisah TAB, mis.
     * "1 JET BLACK (Calya)\tDA 1198 KM\tWANDA 2K TINTING\t1,000\tRp 327.000,00\tRp 0\tRp 327.000,00\tRp 327,00"
     *
     * Ada beberapa bentuk pemisahan karena sel PDF kadang tergabung:
     *  - 8 kolom: no+warna | plat | bahan | qty | harga | diskon | total | hargacc
     *  - 7 kolom: no+warna | plat+bahan (gabung) | qty | ...
     *  - 6 kolom: kepala gabung semua (baris ter-wrap) | qty | ...
     * Semua kolom sebelum 5 kolom terakhir (qty..hargacc) digabung jadi satu
     * "kepala", lalu bahan ditarik dari akhir kepala, warna dari bagian yang
     * diakhiri kurung, sisanya nomor plat.
     */
    public static function parseBarisItemOplosan(string $baris): ?array
    {
        $kolom = explode("\t", rtrim($baris, "\r"));

        if (count($kolom) < 6) {
            return null;
        }

        $tail = array_slice($kolom, -5);           // qty, harga, diskon, total, harga/cc
        $kepala = trim(implode(' ', array_slice($kolom, 0, -5)));

        foreach ($tail as $t) {
            $t = trim($t);

            if ($t !== '0' && ! preg_match('/^(?:Rp\s+)?[\d.,]+$/', $t)) {
                return null;
            }
        }

        if (! preg_match('/^(\d+)\s+(.+)$/', $kepala, $m)) {
            return null;
        }

        $no = (int) $m[1];
        $sisa = trim($m[2]);

        if (! preg_match('/^.*?\s*((?:WANDA|AUTOBASE)\s*(?:2K|SB)?\s*TINTING)$/i', $sisa, $b)) {
            return null;
        }

        $bahan = trim($b[1]);
        $sisa = trim(substr($sisa, 0, -strlen($b[1])));

        // Kalau kolom warna & plat memang terpisah (>= 8 kolom), warnanya
        // dibaca langsung dari kolom pertama. Ini perlu karena ada warna yang
        // ditulis polos tanpa kurung (mis. "Y13"), dan pola kurung di bawah
        // akan gagal untuk bentuk itu.
        if (count($kolom) >= 8) {
            $warna = trim($kolom[0]);
            $warna = trim(substr($warna, strlen((string) $no)));
            $plat = trim($kolom[1]);
        } elseif (preg_match('/^(.*\))\s+(.+)$/', $sisa, $w)) {
            // Plat panjang tergabung dengan warna - warna pasti diakhiri kurung.
            $warna = trim($w[1]);
            $plat = trim($w[2]);
        } else {
            return null; // pola kepala tak terduga - jangan menebak
        }

        $totalItem = self::uangKeFloat(substr(trim($tail[3]), 3));

        return [
            'nomor_urut' => $no,
            'kode_warna_unit' => $warna,
            'no_plat' => $plat,
            'rincian_bahan' => $bahan,
            'qty_cc' => self::qtyKeInt($tail[0]),
            'harga_nota' => $totalItem,
        ];
    }

    // -------------------------------------------------------------- riwayat

    /**
     * Satu baris item riwayat, kolom dipisah TAB, mis.
     * "1 NIK-400\tAMPLAS NIKKEN 400\t20PCS\tRp 7.000,00\t10,00%\tRp 14.000,00\tRp 126.000,00"
     * (nomor urut & kode barang menyatu di kolom pertama, jadi 7 kolom).
     */
    public static function parseBarisItemRiwayat(string $baris): ?array
    {
        $kolom = explode("\t", rtrim($baris, "\r"));

        if (count($kolom) < 7) {
            return null;
        }

        if (! preg_match('/^(\d+)\s*(\S*)$/', trim($kolom[0]), $m)) {
            return null;
        }

        if (! preg_match('/^([\d.,]+)\s*([A-Za-z]+)$/', trim($kolom[2]), $q)) {
            return null;
        }

        return [
            'nomor_urut' => (int) $m[1],
            'kode_barang' => $m[2] !== '' ? $m[2] : null,
            'nama_barang' => trim($kolom[1]),
            'qty' => self::qtyKeInt($q[1]),
            'satuan' => strtoupper($q[2]),
            'harga_satuan' => self::uangKeFloat($kolom[3]),
            'diskon_persen' => self::uangKeFloat(str_replace('%', '', trim($kolom[4]))),
        ];
    }

    // ------------------------------------------------------- pembaca umum

    /**
     * Baca deretan "nota" dari teks PDF lalu pecah jadi baris item.
     *
     * Susunan blok di teks:
     *   Bagian 1 — Wira Toyota Banjarmasin      <- penanda cabang
     *   Nota #1
     *    1 September 2026                        <- tanggal
     *   W.PJ.260901027 · ... · 3 item · 1,450    <- nomor bukti
     *   Rp 515.700,00                           <- total nota
     *   ... baris item ... (diteruskan ke $parseItem)
     *   SUBTOTAL ... / Dokumen dibuat ...       <- penutup nota
     *
     * Baris item yang ter-wrap jadi beberapa baris ditampung dulu, lalu
     * digabung dengan baris berikutnya sampai bisa di-parse. Kalau tetap
     * gagal setelah beberapa baris, tumpukan dibuang supaya tidak salah
     * menggabungkan baris yang bukan item.
     *
     * @param  callable(string):?array  $parseItem
     * @return array<int, array<string, mixed>> baris item + info nota
     */
    public static function bacaNota(string $teks, callable $parseItem): array
    {
        $notas = [];
        $baris = [];
        $cabang = null;
        $cur = null;
        $state = '';
        $pending = [];

        foreach (preg_split('/\R/', $teks) as $barisAsli) {
            $b = trim($barisAsli);

            if ($b === '') {
                continue;
            }

            if (preg_match('/^Bagian\s+\d+\s*\S*\s*(.+)$/u', $b, $m)) {
                $cabang = trim($m[1]);
                $state = '';

                continue;
            }

            if (preg_match('/^Nota #\d+$/', $b)) {
                $pending = [];
                $cur = [
                    'cabang' => $cabang,
                    'tanggal' => null,
                    'no_bukti' => null,
                    'total_nota' => null,
                    'items' => [],
                ];
                $state = 'nota-baru';

                continue;
            }

            if ($cur === null) {
                continue;
            }

            if ($state === 'nota-baru') {
                if (preg_match('/^\d{1,2}\s+\S+\s+\d{4}$/', $b)) {
                    $cur['tanggal'] = self::tanggalIndoKeIso($b);
                    $state = 'info-nota';
                }

                continue;
            }

            if ($state === 'info-nota') {
                if (preg_match('/^([A-Z]{1,3}\.PJ\.\d+)/', $b, $m)) {
                    $cur['no_bukti'] = $m[1];
                    $state = 'total-nota';
                }

                continue;
            }

            if ($state === 'total-nota') {
                if (preg_match('/^Rp\s+[\d.,]+$/', $b)) {
                    $cur['total_nota'] = self::uangKeFloat(substr($b, 3));
                    $state = 'items';
                }

                continue;
            }

            if ($state === 'items') {
                if (str_starts_with($b, 'SUBTOTAL ') || str_starts_with($b, 'Dokumen dibuat')) {
                    if ($pending !== []) {
                        $item = $parseItem(implode(' ', $pending));

                        if ($item !== null) {
                            $cur['items'][] = $item;
                        }

                        $pending = [];
                    }

                    $notas[] = $cur;
                    $cur = null;
                    $state = '';

                    continue;
                }

                if ($pending !== []) {
                    $kandidat = $pending;
                    $kandidat[] = $b;
                    $item = $parseItem(implode(' ', $kandidat));

                    if ($item !== null) {
                        $cur['items'][] = $item;
                        $pending = [];
                    } else {
                        $pending = $kandidat;

                        if (count($pending) > 6) {
                            $pending = [];
                        }
                    }

                    continue;
                }

                $item = $parseItem($b);

                if ($item !== null) {
                    $cur['items'][] = $item;
                } elseif (preg_match('/^\d+\s/', $b) && ! str_contains($b, "\t")) {
                    // Awal item yang pecah di PDF, tunggu baris lanjutannya.
                    $pending = [$b];
                }
            }
        }

        if ($cur !== null) {
            if ($pending !== []) {
                $item = $parseItem(implode(' ', $pending));

                if ($item !== null) {
                    $cur['items'][] = $item;
                }
            }

            if ($cur['items'] !== []) {
                $notas[] = $cur;
            }
        }

        foreach ($notas as $i => $nota) {
            if ($nota['tanggal'] === null || $nota['no_bukti'] === null) {
                throw new RuntimeException('Nota #'.($i + 1).' tidak lengkap (tanggal/no bukti kosong)');
            }

            foreach ($nota['items'] as $urut => $item) {
                $baris[] = $item + [
                    'cabang_area' => $nota['cabang'],
                    'tanggal' => $nota['tanggal'],
                    'no_bukti' => $nota['no_bukti'],
                    'nomor_urut' => $urut + 1,
                ];
            }
        }

        return ['notas' => $notas, 'baris' => $baris];
    }

    public static function oplosan(string $teks): array
    {
        return self::bacaNota($teks, [self::class, 'parseBarisItemOplosan']);
    }

    public static function riwayat(string $teks): array
    {
        return self::bacaNota($teks, [self::class, 'parseBarisItemRiwayat']);
    }

    // --------------------------------------------------------------- harian

    /**
     * Baca teks `harian.txt` (hasil XLSX) jadi baris laporan harian.
     *
     * Susunan kolom (dipisah TAB):
     *   No | Tanggal | Plat Nomor | Kode Warna | Tipe Mobil | Bahan Cat |
     *   Volume Cat (cc) | Jam Dibuat | Jam Selesai | Durasi (Menit) |
     *   Hasil Matching | Keterangan
     *
     * Baris tanpa plat/kode/tanggal dilewati. Tanggal & jam masih berupa
     * angka mentah dari sel Excel, jadi dikonversi dulu. Kolom "Sisa"
     * (cat sisa) dianggap volume 0 karena bukan angka.
     */
    public static function harian(string $teks, string $cabang = 'Wira Toyota Banjarmasin'): array
    {
        $hasil = [];
        $mulai = false;

        foreach (preg_split('/\R/', $teks) as $baris) {
            $kolom = explode("\t", rtrim($baris, "\r"));
            $no = trim($kolom[0] ?? '');

            if (! $mulai) {
                // Cari baris kepala tabel sebagai penanda mulai.
                if (str_starts_with($no, 'No') && str_contains($baris, 'Tanggal')) {
                    $mulai = true;
                }

                continue;
            }

            if (! ctype_digit($no)) {
                continue;
            }

            $tanggal = trim($kolom[1] ?? '');
            $plat = trim($kolom[2] ?? '');

            if ($tanggal === '' || $plat === '') {
                continue; // baris kosong sisa tabel
            }

            $volumeTeks = trim($kolom[6] ?? '');
            $jamDibuat = self::jamExcelKeHi($kolom[7] ?? null);
            $jamSelesai = self::jamExcelKeHi($kolom[8] ?? null);
            $durasi = trim($kolom[9] ?? '');

            $hasil[] = [
                'cabang_area' => $cabang,
                'tanggal' => self::serialExcelKeIso($tanggal),
                'plat_nomor' => $plat,
                'kode_warna' => trim($kolom[3] ?? ''),
                'tipe_mobil' => trim($kolom[4] ?? ''),
                'bahan_cat' => trim($kolom[5] ?? ''),
                // "Sisa" artinya memakai cat sisa, bukan takaran cc -> 0.
                'volume_cc' => preg_match('/^[\d.,]+$/', $volumeTeks)
                    ? self::qtyKeInt($volumeTeks)
                    : 0,
                'jam_dibuat' => $jamDibuat,
                'jam_selesai' => $jamSelesai,
                'durasi_menit' => is_numeric($durasi) ? (int) round((float) $durasi) : null,
                'hasil_matching' => trim($kolom[10] ?? '') ?: 'Sama',
                'keterangan' => trim($kolom[11] ?? '') ?: null,
            ];
        }

        return $hasil;
    }
}
