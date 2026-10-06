<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarianOplosan;
use App\Models\LaporanOplosan;
use App\Models\RiwayatOrder;
use App\Support\Cabang;
use App\Support\NotaGrouper;
use App\Support\Tanggal;
use Illuminate\Http\Request;

/**
 * Halaman khusus role MANAJER.
 *
 * Bedanya dengan halaman tinter: manajer tidak mengisi data, jadi
 * tampilannya diarahkan ke pengawasan - nilai rata-rata, penyimpangan
 * dari kebiasaan, peringkat unit, dan aktivitas terbaru untuk diperiksa.
 */
class ManajerController extends Controller
{
    /**
     * Ambil daftar bulan + tahun yang benar-benar ada datanya.
     * Dipakai untuk mengisi dropdown filter di semua halaman manajer.
     *
     * @return array{tahun: \Illuminate\Support\Collection, bulan: array}
     */
    protected function pilihanPeriode(): array
    {
        $daftarTahun = LaporanOplosan::query()->pluck('tanggal')
            ->merge(RiwayatOrder::query()->pluck('tanggal'))
            ->filter()
            ->map(fn ($tanggal) => (int) $tanggal->format('Y'))
            ->push(now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        return [
            'daftarTahun' => $daftarTahun,
            'namaBulan'   => [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli',
                '08' => 'Agustus', '09' => 'September', '10' => 'Oktober',
                '11' => 'November', '12' => 'Desember',
            ],
        ];
    }

    /**
     * Terapkan filter bulan, tahun & cabang ke query.
     */
    protected function filterPeriode($query, ?string $bulan, ?string $tahun, ?string $cabang = null)
    {
        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }
        if ($cabang) {
            $query->where('cabang_area', $cabang);
        }

        return $query;
    }

    /**
     * ================= DASHBOARD PENGAWASAN =================
     * Fokus manajer: seberapa besar belanja, apakah ada oplosan yang
     * biayanya jauh di atas kebiasaan, dan unit mana yang paling sering
     * dikerjakan.
     */
    public function dashboard(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun', now()->year);

        $oplosan = $this->filterPeriode(LaporanOplosan::query(), $bulan, $tahun)->get();
        $orders = $this->filterPeriode(RiwayatOrder::query(), $bulan, $tahun)->get();

        // ===== KPI utama =====
        $totalBelanja = $orders->sum(fn ($order) => $order->total_item);
        $totalBiayaOplosan = $oplosan->sum('harga_nota');
        $totalPengeluaran = $totalBelanja + $totalBiayaOplosan;
        $totalCc = $oplosan->sum('qty_cc');

        $rataOplosan = $oplosan->count() > 0 ? $totalBiayaOplosan / $oplosan->count() : 0;
        $rataOrder = $orders->count() > 0 ? $totalBelanja / $orders->count() : 0;

        // Biaya oplosan per CC - dipakai untuk menilai kewajaran tinting.
        $biayaPerCc = $totalCc > 0 ? $totalBiayaOplosan / $totalCc : 0;

        // ===== Perbandingan dengan bulan sebelumnya =====
        $awalBulanIni = now()->startOfMonth();
        $awalBulanLalu = now()->subMonthNoOverflow()->startOfMonth();
        $akhirBulanLalu = now()->subMonthNoOverflow()->endOfMonth();

        $belanjaBulanIni = RiwayatOrder::where('tanggal', '>=', $awalBulanIni)
            ->get()->sum(fn ($order) => $order->total_item);
        $belanjaBulanLalu = RiwayatOrder::whereBetween('tanggal', [$awalBulanLalu, $akhirBulanLalu])
            ->get()->sum(fn ($order) => $order->total_item);

        // Persentase perubahan; null artinya belum ada pembanding.
        $perubahanBelanja = $belanjaBulanLalu > 0
            ? (($belanjaBulanIni - $belanjaBulanLalu) / $belanjaBulanLalu) * 100
            : null;

        // ===== Perlu diperiksa: biaya oplosan jauh di atas rata-rata =====
        // Ambang batas 1,5x rata-rata - cukup longgar supaya tidak berisik,
        // tapi tetap menangkap catatan yang mencolok.
        $ambangPerhatian = $rataOplosan * 1.5;

        $perluPerhatian = $rataOplosan > 0
            ? $oplosan->filter(fn ($laporan) => (float) $laporan->harga_nota > $ambangPerhatian)
                ->sortByDesc('harga_nota')
                ->take(5)
                ->values()
            : collect();

        // ===== Ringkasan bulan berjalan =====
        $ringkasBulanIni = [
            'belanja'     => $belanjaBulanIni,
            'perubahan'   => $perubahanBelanja,
            'order'       => RiwayatOrder::where('tanggal', '>=', $awalBulanIni)->count(),
            'oplosan'     => LaporanOplosan::where('tanggal', '>=', $awalBulanIni)->count(),
            'cc'          => LaporanOplosan::where('tanggal', '>=', $awalBulanIni)->get()->sum('qty_cc'),
        ];

        $periode = $this->pilihanPeriode();

        return view('manajer.dashboard', [
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'daftarTahun'       => $periode['daftarTahun'],
            'namaBulan'         => $periode['namaBulan'],
            'totalBelanja'      => $totalBelanja,
            'totalBiayaOplosan' => $totalBiayaOplosan,
            'totalPengeluaran'  => $totalPengeluaran,
            'totalCc'           => $totalCc,
            'totalOplosan'      => $oplosan->count(),
            'totalOrder'        => $orders->count(),
            'rataOplosan'       => $rataOplosan,
            'rataOrder'         => $rataOrder,
            'biayaPerCc'        => $biayaPerCc,
            'perluPerhatian'    => $perluPerhatian,
            'ambangPerhatian'   => $ambangPerhatian,
            'ringkasBulanIni'   => $ringkasBulanIni,
            'tren'              => $this->trenBulanan($bulan ?: now()->month, $tahun ?: now()->year),
            'topUnit'           => $this->topUnit($bulan, $tahun),
            'topPemasok'        => $this->topPemasok($bulan, $tahun),
            'aktivitas'         => $this->aktivitasTerbaru($bulan, $tahun),
        ]);
    }

    /**
     * Tren 6 bulan: jumlah laporan vs nilai belanja.
     */
    protected function trenBulanan($bulan, $tahun): array
    {
        $akhir = now()->setDate((int) $tahun, (int) $bulan, 1)->startOfMonth();
        $awal = $akhir->copy()->subMonths(5);

        $oplosan = LaporanOplosan::whereBetween('tanggal', [$awal->copy()->startOfMonth(), $akhir->copy()->endOfMonth()])
            ->get()
            ->groupBy(fn ($item) => $item->tanggal->format('Y-m'));

        $orders = RiwayatOrder::whereBetween('tanggal', [$awal->copy()->startOfMonth(), $akhir->copy()->endOfMonth()])
            ->get()
            ->groupBy(fn ($item) => $item->tanggal->format('Y-m'));

        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $hasil = [];

        for ($i = 0; $i < 6; $i++) {
            $kursor = $awal->copy()->addMonths($i);
            $kunci = $kursor->format('Y-m');
            $barisOplosan = $oplosan->get($kunci, collect());
            $barisOrder = $orders->get($kunci, collect());

            $hasil[] = [
                'label'   => $namaBulan[$kursor->month - 1] . ' / ' . $kursor->format('y'),
                'oplosan' => $barisOplosan->count(),
                'cc'      => $barisOplosan->sum('qty_cc'),
                'order'   => $barisOrder->count(),
                'belanja' => $barisOrder->sum(fn ($order) => $order->total_item),
            ];
        }

        return $hasil;
    }

    /**
     * Unit (no. plat) dengan pemakaian terbanyak - bahan pembanding
     * kalau ada selisih biaya antar unit.
     */
    protected function topUnit($bulan, $tahun)
    {
        $query = $this->filterPeriode(LaporanOplosan::query(), $bulan, $tahun);

        return $query->get()
            ->groupBy(fn ($laporan) => $laporan->no_plat)
            ->map(function ($baris) {
                return [
                    'plat'   => $baris->first()->no_plat,
                    'warna'  => $baris->first()->kode_warna_unit,
                    'jumlah' => $baris->count(),
                    'cc'     => $baris->sum('qty_cc'),
                    'biaya'  => $baris->sum('harga_nota'),
                ];
            })
            ->sortByDesc('cc')
            ->take(5)
            ->values();
    }

    /**
     * Barang yang paling banyak menyerap anggaran (urut Rupiah).
     */
    protected function topPemasok($bulan, $tahun)
    {
        $query = $this->filterPeriode(RiwayatOrder::query(), $bulan, $tahun);

        return $query->get()
            ->groupBy(fn ($order) => $order->nama_barang)
            ->map(function ($baris) {
                return [
                    'nama'    => $baris->first()->nama_barang,
                    'qty'     => $baris->sum('qty'),
                    'belanja' => $baris->sum(fn ($order) => $order->total_item),
                ];
            })
            ->sortByDesc('belanja')
            ->take(5)
            ->values();
    }

    /**
     * Gabungan aktivitas terbaru dari kedua jenis data.
     */
    protected function aktivitasTerbaru($bulan, $tahun)
    {
        // Titik-tengah dirakit dari kode byte supaya file ini tetap ASCII.
        $tt = chr(0xC2) . chr(0xB7);

        $laporan = $this->filterPeriode(LaporanOplosan::query(), $bulan, $tahun)
            ->orderByDesc('tanggal')->orderByDesc('id')->take(6)->get()
            ->map(function ($item) use ($tt) {
                return [
                    'jenis'   => 'oplosan',
                    'tanggal' => $item->tanggal,
                    'judul'   => Tanggal::panjang($item->tanggal),
                    'rincian' => $item->no_plat . " $tt " . $item->rincian_bahan,
                    'nilai'   => 'Rp ' . number_format((float) $item->harga_nota, 0, ',', '.'),
                    'nominal' => (float) $item->harga_nota,
                ];
            });

        $order = $this->filterPeriode(RiwayatOrder::query(), $bulan, $tahun)
            ->orderByDesc('tanggal')->orderByDesc('id')->take(6)->get()
            ->map(function ($item) use ($tt) {
                return [
                    'jenis'   => 'order',
                    'tanggal' => $item->tanggal,
                    'judul'   => Tanggal::panjang($item->tanggal),
                    'rincian' => $item->no_bukti_faktur . " $tt " . $item->qty . " $tt " . $item->satuan,
                    'nilai'   => 'Rp ' . number_format((float) $item->total_item, 0, ',', '.'),
                    'nominal' => (float) $item->total_item,
                ];
            });

        return $laporan->merge($order)
            ->sortByDesc('tanggal')
            ->take(6)
            ->values();
    }

    /**
     * ================= RIWAYAT ORDER (PANTAU) =================
     * Daftar pembelian untuk diperiksa. Ada kolom total per barang
     * supaya manajer cepat melihat pos biaya terbesar.
     */
    public function riwayatOrder(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $cabang = $request->input('cabang');

        $semua = $this->filterPeriode(RiwayatOrder::query(), $bulan, $tahun, $cabang)
            ->orderBy('tanggal')->orderBy('nomor_urut')->orderBy('id')->get();

        $periode = $this->pilihanPeriode();

        $semuaNota = NotaGrouper::withTotals(
            NotaGrouper::group(
                $semua,
                fn ($order) => $order->nomor_bukti,
                fn ($order) => $order->no_bukti_faktur,
            ),
            fn ($order) => (float) $order->total_item,
        );

        return view('manajer.riwayat_order', [
            // Dipaginasi per nota supaya barang satu nota tidak terpisah halaman.
            'notas'       => RiwayatOrderController::paginateNotas($semuaNota, 10, $request),
            'jumlahNota'  => $semuaNota->count(),
            'totalNilaiNota' => $semuaNota->sum('total'),
            'perCabang'   => $this->ringkasanPerCabang($semuaNota),
            'cabang'      => $cabang,
            'rataPerNota' => $semuaNota->count() > 0
                ? $semuaNota->sum('total') / $semuaNota->count()
                : 0,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'daftarTahun' => $periode['daftarTahun'],
            'namaBulan'   => $periode['namaBulan'],
            'totalBelanja' => $semua->sum(fn ($order) => $order->total_item),
            'jumlahBaris' => $semua->count(),
            'jumlahBarang' => $semua->unique('nama_barang')->count(),
            'totalDiskon' => $semua->sum(fn ($order) => $order->qty * $order->harga_satuan * ($order->diskon_persen / 100)),
            'barangTermahal' => $semua->sortByDesc(fn ($order) => $order->total_item)->take(5)->values(),
        ]);
    }

    /**
     * Ringkasan jumlah nota & nilai per cabang dari kumpulan nota.
     */
    protected function ringkasanPerCabang($notas)
    {
        return $notas
            ->groupBy(fn ($nota) => $nota['items']->first()->cabang_area ?: Cabang::default())
            ->map(function ($baris, $nama) {
                return [
                    'cabang' => $nama,
                    'nota'   => $baris->count(),
                    'nilai'  => $baris->sum('total'),
                ];
            })
            ->sortByDesc('nilai')
            ->values();
    }

    /**
     * ================= LAPORAN OPLOSAN (PANTAU) =================
     * Daftar tinting per unit, lengkap dengan penanda biaya yang
     * mencolok agar mudah ditelusuri.
     */
    public function laporanOplosan(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $cabang = $request->input('cabang');

        $semua = $this->filterPeriode(LaporanOplosan::query(), $bulan, $tahun, $cabang)
            ->orderBy('tanggal')->orderBy('nomor_urut')->orderBy('id')->get();

        $totalBiaya = $semua->sum('harga_nota');
        $rataBiaya = $semua->count() > 0 ? $totalBiaya / $semua->count() : 0;
        $ambangPerhatian = $rataBiaya * 1.5;

        $periode = $this->pilihanPeriode();

        $notas = NotaGrouper::withTotals(
            NotaGrouper::group(
                $semua,
                fn ($laporan) => $laporan->nomor_nota,
                fn ($laporan) => $laporan->no_bukti_nota,
            ),
            fn ($laporan) => (float) $laporan->harga_nota,
        );

        return view('manajer.laporan_oplosan', [
            'notas'       => RiwayatOrderController::paginateNotas($notas, 10, $request),
            'jumlahNota'  => $notas->count(),
            'totalNilaiNota' => $notas->sum('total'),
            'perCabang'   => $this->ringkasanPerCabang($notas),
            'cabang'      => $cabang,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'daftarTahun' => $periode['daftarTahun'],
            'namaBulan'   => $periode['namaBulan'],
            'totalLaporan' => $semua->count(),
            'totalCc'     => $semua->sum('qty_cc'),
            'totalBiaya'  => $totalBiaya,
            'rataBiaya'   => $rataBiaya,
            'ambangPerhatian' => $ambangPerhatian,
            'biayaPerCc'  => $semua->sum('qty_cc') > 0 ? $totalBiaya / $semua->sum('qty_cc') : 0,
            'perluPerhatian' => $rataBiaya > 0
                ? $semua->filter(fn ($laporan) => (float) $laporan->harga_nota > $ambangPerhatian)
                    ->sortByDesc('harga_nota')->take(5)->values()
                : collect(),
        ]);
    }

    /**
     * ================= LAPORAN HARIAN (PANTAU) =================
     * Rekap pekerjaan oplosan harian per unit. Yang disorot di sini angka
     * yang menunjukkan mutu kerja: berapa persen warna yang matching sama,
     * berapa lama pengerjaannya, dan unit mana yang perlu ditelusuri.
     */
    public function laporanHarian(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $cabang = $request->input('cabang');

        $semua = $this->filterPeriode(LaporanHarianOplosan::query(), $bulan, $tahun, $cabang)
            ->orderBy('tanggal')->orderBy('jam_dibuat')->orderBy('id')->get();

        $totalBaris = $semua->count();
        $jumlahSama = $semua->where('hasil_matching', 'Sama')->count();
        $totalDurasi = $semua->sum(fn ($b) => (int) $b->durasi_menit);

        // Unit dengan volume terbesar - pembanding kalau ada pemakaian mencolok.
        $perUnit = $semua
            ->groupBy('plat_nomor')
            ->map(function ($baris, $plat) {
                $jumlah = $baris->count();
                $sama = $baris->where('hasil_matching', 'Sama')->count();

                return [
                    'plat'    => $plat,
                    'warna'   => $baris->last()->kode_warna,
                    'tipe'    => $baris->last()->tipe_mobil,
                    'jumlah'  => $jumlah,
                    'volume'  => $baris->sum('volume_cc'),
                    'durasi'  => $baris->sum(fn ($b) => (int) $b->durasi_menit),
                    'sama'    => $sama,
                    'persen'  => $jumlah > 0 ? round($sama / $jumlah * 100) : 0,
                ];
            })
            ->sortByDesc('volume')
            ->take(5)
            ->values();

        // Baris yang hasil matching-nya belum sama - perlu diperiksa ulang.
        $belumSama = $semua
            ->where('hasil_matching', '!=', 'Sama')
            ->sortByDesc('tanggal')
            ->take(5)
            ->values();

        $periode = $this->pilihanPeriode();

        return view('manajer.laporan_harian', [
            'baris'        => $semua->sortByDesc('tanggal')->take(20)->values(),
            'jumlahBaris'  => $totalBaris,
            'totalVolume'  => $semua->sum('volume_cc'),
            'totalDurasi'  => $totalDurasi,
            'rataDurasi'   => $totalBaris > 0 ? (int) round($totalDurasi / $totalBaris) : 0,
            'jumlahSama'   => $jumlahSama,
            'persenSama'   => $totalBaris > 0 ? round($jumlahSama / $totalBaris * 100) : 0,
            'perUnit'      => $perUnit,
            'belumSama'    => $belumSama,
            'perCabang'    => $semua
                ->groupBy(fn ($b) => $b->cabang_area ?: Cabang::default())
                ->map(fn ($baris, $nama) => [
                    'cabang' => $nama,
                    'baris'  => $baris->count(),
                    'volume' => $baris->sum('volume_cc'),
                ])
                ->sortByDesc('volume')
                ->values(),
            'cabang'       => $cabang,
            'bulan'        => $bulan,
            'tahun'        => $tahun,
            'daftarTahun'  => $periode['daftarTahun'],
            'namaBulan'    => $periode['namaBulan'],
        ]);
    }
}

