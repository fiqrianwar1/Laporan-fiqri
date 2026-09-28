<?php

namespace App\Http\Controllers;

use App\Models\LaporanOplosan;
use App\Models\RiwayatOrder;
use App\Support\Cabang;
use App\Support\NotaGrouper;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Halaman dashboard: ringkasan statistik oplosan & riwayat order.
     */
    public function index(Request $request)
    {
        // ===== Periode terpilih (default: tahun berjalan) =====
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun', now()->year);
        $cabang = $request->input('cabang');
        $semuaPeriode = !$bulan && !$tahun;

        $filterPeriode = function ($query) use ($bulan, $tahun, $cabang) {
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
        };

        // Data periode terpilih dipakai untuk kartu statistik & daftar terbaru.
        $oplosan = $filterPeriode(LaporanOplosan::query())->get();
        $orders = $filterPeriode(RiwayatOrder::query())->get();

        $totalOplosan = $oplosan->count();
        $totalCc = $oplosan->sum('qty_cc');
        $totalBiayaOplosan = $oplosan->sum('harga_nota');
        $totalOrder = $orders->count();
        $totalBelanja = $orders->sum(fn ($order) => $order->total_item);

        // Nilai rata-rata per transaksi (hindari pembagian nol).
        $rataOplosan = $totalOplosan > 0 ? $totalBiayaOplosan / $totalOplosan : 0;
        $rataOrder = $totalOrder > 0 ? $totalBelanja / $totalOrder : 0;

        // Statistik bulan berjalan (selalu dihitung terpisah dari filter).
        $awalBulanIni = now()->startOfMonth();
        $orderBulanIni = RiwayatOrder::where('tanggal', '>=', $awalBulanIni)->get();
        $oplosanBulanIni = LaporanOplosan::where('tanggal', '>=', $awalBulanIni)->get();

        $ringkasBulanIni = [
            'belanja' => $orderBulanIni->sum(fn ($order) => $order->total_item),
            'order'   => $orderBulanIni->count(),
            'oplosan' => $oplosanBulanIni->count(),
            'cc'      => $oplosanBulanIni->sum('qty_cc'),
        ];

        // Pilihan tahun untuk filter.
        $daftarTahun = LaporanOplosan::query()->pluck('tanggal')
            ->merge(RiwayatOrder::query()->pluck('tanggal'))
            ->filter()
            ->map(fn ($tanggal) => (int) $tanggal->format('Y'))
            ->push(now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        // ===== Ringkasan PER CABANG =====
        // Total nota & nilai dihitung per nota (bukan per baris item),
        // supaya angka totalnya sama dengan yang ada di halaman detail.
        $notaOplosan = NotaGrouper::withTotals(
            NotaGrouper::group(
                $oplosan,
                fn ($laporan) => $laporan->nomor_nota,
                fn ($laporan) => $laporan->no_bukti_nota,
            ),
            fn ($laporan) => (float) $laporan->harga_nota,
        );

        $notaOrder = NotaGrouper::withTotals(
            NotaGrouper::group(
                $orders,
                fn ($order) => $order->nomor_bukti,
                fn ($order) => $order->no_bukti_faktur,
            ),
            fn ($order) => (float) $order->total_item,
        );

        $perCabang = $this->ringkasanPerCabang($notaOplosan, $notaOrder);

        // Pilihan bulan (dipakai juga untuk tren bila salah satu belum diisi).
        $bulanTren = $bulan ?: now()->month;
        $tahunTren = $tahun ?: now()->year;

        return view('dashboard', [
            'bulan'            => $bulan,
            'tahun'            => $tahun,
            'semuaPeriode'     => $semuaPeriode,
            'cabang'           => $cabang,
            'daftarTahun'      => $daftarTahun,
            'perCabang'        => $perCabang,
            'totalOplosan'     => $totalOplosan,
            'totalCc'          => $totalCc,
            'totalBiayaOplosan'=> $totalBiayaOplosan,
            'totalOrder'       => $totalOrder,
            'totalBelanja'     => $totalBelanja,
            'rataOplosan'      => $rataOplosan,
            'rataOrder'        => $rataOrder,
            'ringkasBulanIni'  => $ringkasBulanIni,
            'tren'             => $this->trenBulanan($bulanTren, $tahunTren),
            'topBarang'        => $this->topBarang($bulan, $tahun),
            'topWarna'         => $this->topWarna($bulan, $tahun),
            'oplosanTerbaru'   => $this->oplosanTerbaru($bulan, $tahun),
            'orderTerbaru'     => $this->orderTerbaru($bulan, $tahun),
            'semuaPeriode'     => $semuaPeriode,
        ]);
    }

    /**
     * Ringkasan per cabang: jumlah nota & nilai dari kedua jenis data.
     *
     * Cabang yang belum pernah dipakai tetap ditampilkan dengan nilai nol,
     * supaya manajer bisa langsung membandingkan kedua cabang.
     */
    protected function ringkasanPerCabang($notaOplosan, $notaOrder)
    {
        $kelompok = [];

        foreach (Cabang::daftar() as $nama) {
            $kelompok[$nama] = [
                'cabang'          => $nama,
                'nota_oplosan'    => 0,
                'nilai_oplosan'   => 0.0,
                'cc_oplosan'      => 0,
                'nota_order'      => 0,
                'nilai_order'     => 0.0,
                'total_nota'      => 0,
                'total_nilai'     => 0.0,
            ];
        }

        foreach ($notaOplosan as $nota) {
            $nama = $nota['items']->first()->cabang_area ?: Cabang::default();
            $kelompok[$nama] ??= $this->kerangkaCabang($nama);

            $kelompok[$nama]['nota_oplosan']  += 1;
            $kelompok[$nama]['nilai_oplosan'] += (float) $nota['total'];
            $kelompok[$nama]['cc_oplosan']    += (int) $nota['items']->sum('qty_cc');
        }

        foreach ($notaOrder as $nota) {
            $nama = $nota['items']->first()->cabang_area ?: Cabang::default();
            $kelompok[$nama] ??= $this->kerangkaCabang($nama);

            $kelompok[$nama]['nota_order']  += 1;
            $kelompok[$nama]['nilai_order'] += (float) $nota['total'];
        }

        return collect($kelompok)
            ->map(function ($baris) {
                $baris['total_nota']  = $baris['nota_oplosan'] + $baris['nota_order'];
                $baris['total_nilai'] = $baris['nilai_oplosan'] + $baris['nilai_order'];

                return $baris;
            })
            ->sortByDesc('total_nilai')
            ->values();
    }

    /**
     * Kerangka nilai nol untuk cabang yang belum terdaftar di daftar resmi.
     */
    protected function kerangkaCabang(string $nama): array
    {
        return [
            'cabang'        => $nama,
            'nota_oplosan'  => 0,
            'nilai_oplosan' => 0.0,
            'cc_oplosan'    => 0,
            'nota_order'    => 0,
            'nilai_order'   => 0.0,
            'total_nota'    => 0,
            'total_nilai'   => 0.0,
        ];
    }

    /**
     * Tren 6 bulan terakhir (diakhiri bulan terpilih).
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
     * Barang paling sering dibeli (urut qty).
     */
    protected function topBarang($bulan, $tahun)
    {
        $query = RiwayatOrder::query();

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        return $query->get()
            // Satu barang bisa muncul beberapa kali karena satu nota boleh
            // berisi banyak item, jadi digabung per nama barang.
            ->groupBy(fn ($order) => $order->nama_barang)
            ->map(function ($baris) {
                return [
                    'nama'    => $baris->first()->nama_barang,
                    'satuan'  => $baris->first()->satuan,
                    'qty'     => $baris->sum('qty'),
                    'belanja' => $baris->sum(fn ($order) => (float) $order->total_item),
                ];
            })
            ->sortByDesc('qty')
            ->take(5)
            ->values();
    }

    /**
     * Kode warna unit yang paling sering dioplos.
     */
    protected function topWarna($bulan, $tahun)
    {
        $query = LaporanOplosan::query();

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        return $query->get()
            ->groupBy(fn ($laporan) => $laporan->kode_warna_unit)
            ->map(function ($baris) {
                return [
                    'kode'    => $baris->first()->kode_warna_unit,
                    'unit'    => $baris->unique('no_plat')->count(),
                    'cc'      => $baris->sum('qty_cc'),
                    'biaya'   => $baris->sum('harga_nota'),
                ];
            })
            ->sortByDesc('cc')
            ->take(5)
            ->values();
    }

    /**
     * Laporan oplosan terbaru.
     */
    protected function oplosanTerbaru($bulan, $tahun)
    {
        $query = LaporanOplosan::query();

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        return $query->orderByDesc('tanggal')->orderByDesc('nomor_urut')->orderByDesc('id')->get();
    }

    /**
     * Riwayat order terbaru.
     */
    protected function orderTerbaru($bulan, $tahun)
    {
        $query = RiwayatOrder::query();

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $orders = $query->orderByDesc('tanggal')->orderByDesc('nomor_urut')->orderByDesc('id')->get();

        // Ditampilkan sebagai daftar nota terbaru, bukan daftar baris barang,
        // supaya satu nota berisi 7 item tidak menutupi nota lain.
        return NotaGrouper::withTotals(
            NotaGrouper::group(
                $orders,
                fn ($order) => $order->nomor_bukti,
                fn ($order) => $order->no_bukti_faktur,
            ),
            fn ($order) => (float) $order->total_item,
        )->sortByDesc(fn ($nota) => $nota['tanggal']?->timestamp ?? 0)->take(5)->values();
    }
}
