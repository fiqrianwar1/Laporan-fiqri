<?php

namespace App\Http\Controllers;

use App\Models\LaporanOplosan;
use App\Models\RiwayatOrder;
use App\Support\Cabang;
use App\Support\NotaGrouper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // ===== Daftar aktivitas dipaginasi =====
        // Zaman data sudah menumpuk, menampilkan seluruh riwayat bikin
        // halaman dashboard jadi sangat panjang. Jadi yang tampil cukup
        // beberapa per halaman, sisanya dibuka lewat tombol halaman.
        // Query dasar + urutannya sama persis dengan method aslinya,
        // hanya hasil akhirnya dipotong per halaman.
        $aktivitasOplosan = $this->kueriOplosanTerbaru($bulan, $tahun)->paginate(6, ['*'], 'hal_oplosan')->withQueryString();
        $aktivitasOrder = $this->paginatorNotaOrderTerbaru($bulan, $tahun, 5, 'hal_order')->withQueryString();

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
            'aktivitasOplosan' => $aktivitasOplosan,
            'aktivitasOrder'   => $aktivitasOrder,
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
     * Laporan oplosan terbaru (seluruhnya - dipakai sebagai ringkasan
     * cepat, mis. untuk menghitung "Lihat semua").
     */
    protected function oplosanTerbaru($bulan, $tahun)
    {
        return $this->kueriOplosanTerbaru($bulan, $tahun)->get();
    }

    /**
     * Query dasar laporan oplosan terbaru, urut dari yang paling baru.
     * Dipakai oleh oplosanTerbaru() dan versi paginasi di dashboard.
     */
    protected function kueriOplosanTerbaru($bulan, $tahun)
    {
        $query = LaporanOplosan::query();

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        return $query->orderByDesc('tanggal')->orderByDesc('nomor_urut')->orderByDesc('id');
    }

    /**
     * Riwayat order terbaru (dikelompokkan per nota).
     */
    protected function orderTerbaru($bulan, $tahun)
    {
        return $this->kelompokkanNotaOrder($this->kueriOrderTerbaru($bulan, $tahun)->get());
    }

    /**
     * Query dasar baris order terbaru. Barisnya dikelompokkan jadi nota oleh
     * kelompokkanNotaOrder() sebelum ditampilkan.
     */
    protected function kueriOrderTerbaru($bulan, $tahun)
    {
        $query = RiwayatOrder::query();

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }
        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        return $query->orderByDesc('tanggal')->orderByDesc('nomor_urut')->orderByDesc('id');
    }

    /**
     * Ambil daftar nota terbaru dalam bentuk PAGINATOR, siap dipakai
     * komponen x-pager.
     *
     * Tidak bisa langsung memakai query group-by untuk ditampilkan: hasil
     * group by hanya berisi kolom ringkasan (id, tanggal), bukan model
     * lengkap - padahal nota butuh seluruh itemnya (nama barang, qty, harga).
     *
     * Jadi dikerjakan dua langkah:
     *   1. paginator memotong DAFTAR KUNCI nota (satu baris = satu nomor bukti);
     *   2. kunci di halaman itu baru dipakai mengambil seluruh item aslinya,
     *      lalu dikelompokkan seperti biasa.
     * Hasilnya tetap satu halaman = satu nota utuh, tidak terbelah.
     */
    protected function paginatorNotaOrderTerbaru($bulan, $tahun, int $perHalaman = 5, string $namaHalaman = 'hal_order')
    {
        $halaman = max(1, (int) request()->input($namaHalaman, 1));

        // Langkah 1: ambil daftar kunci nota (satu baris = satu nomor bukti),
        // diurutkan dari yang paling baru. Di sini yang dipotong per halaman.
        $semuaKunci = $this->daftarKunciNota($bulan, $tahun);
        $total = $semuaKunci->count();
        $kunci = $semuaKunci->slice(($halaman - 1) * $perHalaman, $perHalaman)->values()->all();

        // Langkah 2: ambil seluruh item milik nota-nota di halaman ini dalam
        // satu query (tidak ada N+1), baru dikelompokkan seperti biasa.
        $notas = collect();

        if (! empty($kunci)) {
            $items = RiwayatOrder::query()
                ->when($bulan, fn ($q) => $q->whereMonth('tanggal', $bulan))
                ->when($tahun, fn ($q) => $q->whereYear('tanggal', $tahun))
                ->orderByDesc('tanggal')->orderByDesc('nomor_urut')->orderByDesc('id')
                ->get()
                // Disaring di PHP memakai kunci yang sama dengan pengelompokan,
                // supaya "SP/123" dan "SP123" tetap dianggap nota yang sama.
                ->filter(fn ($order) => in_array($order->nomor_bukti, $kunci, true));

            $perKunci = $this->kelompokkanNotaOrder($items)
                ->keyBy(fn ($nota) => $this->kunciNota($nota));

            // Urutkan sesuai urutan halaman, bukan urutan hasil grouping.
            $notas = collect($kunci)->map(fn ($k) => $perKunci->get($k))->filter()->values();
        }

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $notas,
            $total,
            $perHalaman,
            $halaman,
            ['path' => request()->url(), 'query' => request()->query()],
        );
    }

    /**
     * Daftar kunci nota (nomor bukti ternormalisasi) urut dari yang terbaru.
     * Dipakai untuk menentukan nota mana yang masuk halaman berapa.
     */
    protected function daftarKunciNota($bulan, $tahun)
    {
        return $this->kueriOrderTerbaru($bulan, $tahun)
            ->get()
            ->map(fn ($order) => $order->nomor_bukti)
            ->unique()
            ->values();
    }

    /**
     * Kunci pengelompokan satu nota - disamakan dengan versi SQL
     * (nomor bukti tanpa spasi/tanda baca, huruf besar).
     */
    protected function kunciNota(array $nota): string
    {
        $nomor = preg_replace('/[^A-Za-z0-9]/', '', (string) ($nota['nomor'] ?? ''));

        return $nomor !== '' ? strtoupper($nomor) : 'TANPA-NOMOR';
    }

    /**
     * Ubah baris order jadi daftar nota lengkap dengan jumlah item dan
     * total nilainya, urut dari nota terbaru.
     */
    protected function kelompokkanNotaOrder($orders)
    {
        // Ditampilkan sebagai daftar nota terbaru, bukan daftar baris barang,
        // supaya satu nota berisi 7 item tidak menutupi nota lain.
        return NotaGrouper::withTotals(
            NotaGrouper::group(
                $orders,
                fn ($order) => $order->nomor_bukti,
                fn ($order) => $order->no_bukti_faktur,
            ),
            fn ($order) => (float) $order->total_item,
        )
            ->sortByDesc(fn ($nota) => $nota['tanggal']?->timestamp ?? 0)
            ->values();
    }
}