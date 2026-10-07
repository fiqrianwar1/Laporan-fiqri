<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Support\Cabang;
use App\Support\NotaGrouper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Surat jalan (barang keluar gudang).
 *
 * Polanya sama dengan laporan oplosan & riwayat order: satu surat bisa berisi
 * banyak barang, jadi barisnya dikelompokkan lewat nomor surat + tanggal.
 * Bedanya, isi tiap barang mengikuti form surat jalan (kode barang, nama,
 * kemasan, jumlah, asal penyimpanan, keterangan).
 */
class SuratJalanController extends Controller
{
    /**
     * Jumlah surat per halaman di daftar.
     * Disamakan dengan daftar lain supaya panjang halamannya seragam.
     */
    public const SURAT_PER_HALAMAN = 10;

    /**
     * Ambil query surat jalan, difilter bulan, tahun & cabang kalau ada.
     */
    protected function filteredQuery(Request $request)
    {
        $query = SuratJalan::query();

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->input('bulan'));
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->input('tahun'));
        }
        if ($request->filled('cabang')) {
            $query->where('cabang_area', $request->input('cabang'));
        }

        return $query->orderBy('tanggal')->orderBy('nomor_urut')->orderBy('id');
    }

    /**
     * Kelompokkan baris menjadi surat (1 surat bisa banyak barang).
     */
    protected function groupedSurat($baris)
    {
        return NotaGrouper::withTotals(
            NotaGrouper::group(
                $baris,
                fn ($item) => $item->nomor_surat_bersih,
                fn ($item) => $item->nomor_surat,
            ),
            // "Nilai" sebuah surat adalah total jumlah barangnya.
            fn ($item) => (float) $item->jumlah,
        );
    }

    /**
     * Tampilkan semua surat jalan + ringkasan.
     *
     * Tabelnya dipaginasi PER SURAT supaya barang satu surat tetap utuh
     * dalam satu kartu, tidak terbelah ke halaman berikutnya.
     */
    public function index(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $cabang = $request->input('cabang');

        // Ringkasan dihitung dari seluruh data periode terpilih.
        $semua = $this->filteredQuery($request)->get();

        $totalBaris = $semua->count();
        $totalJumlah = $semua->sum('jumlah');

        $semuaSurat = $this->groupedSurat($semua);
        $totalSurat = $semuaSurat->count();
        $rataPerSurat = $totalSurat > 0 ? $totalJumlah / $totalSurat : 0;

            // Barang yang paling sering dikirim pada periode ini.
            $perBarang = $semua
                ->groupBy(fn ($item) => $item->nama_barang)
                ->map(fn ($baris, $nama) => [
                    'barang' => $nama,
                    'kali'   => $baris->count(),
                    'jumlah' => $baris->sum('jumlah'),
                ])
                ->sortByDesc('jumlah')
                ->take(6)
                ->values();

            // Ringkasan per cabang supaya kontribusi tiap area tetap terlihat.
            $totalJumlahAman = $totalJumlah > 0 ? $totalJumlah : 1;
            $perCabang = $semuaSurat
                ->groupBy(fn ($surat) => $surat['items']->first()->cabang_area ?: Cabang::default())
                ->map(fn ($baris, $nama) => [
                    'cabang' => $nama,
                    'surat'  => $baris->count(),
                    'barang' => $baris->sum(fn ($s) => $s['jumlahItem']),
                    'jumlah' => $baris->sum(fn ($s) => $s['items']->sum('jumlah')),
                    'persen' => round(($baris->sum(fn ($s) => $s['items']->sum('jumlah')) / $totalJumlahAman) * 100),
                ])
                ->sortByDesc('jumlah')
                ->values();

            $suratHalaman = RiwayatOrderController::paginateNotas(
                $semuaSurat,
                self::SURAT_PER_HALAMAN,
                $request,
            );

            return view('surat_jalan.index', compact(
                'suratHalaman', 'totalBaris', 'totalJumlah', 'totalSurat', 'rataPerSurat',
                'perBarang', 'perCabang', 'bulan', 'tahun', 'cabang'
            ));
        }

    /**
     * Tampilkan form tambah surat jalan.
     */
    public function create()
    {
        return view('surat_jalan.create');
    }

    /**
     * Simpan satu barang surat jalan baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validasi($request);

        // Kalau nomor urut dikosongkan, barang ini ditaruh di urutan terakhir
        // surat tersebut.
        $validated['nomor_urut'] = $validated['nomor_urut'] ?? $this->nomorUrutBerikutnya($validated);

        SuratJalan::create($validated);

        $pesan = 'Surat jalan berhasil ditambahkan.';
        $jumlahBarang = $this->jumlahBarangSurat($validated);

        if ($jumlahBarang > 1) {
            $pesan .= " Surat {$validated['nomor_surat']} sekarang berisi {$jumlahBarang} barang.";
        }

        return redirect()
            ->route('surat-jalan.index')
            ->with('success', $pesan);
    }

    /**
     * Tampilkan form edit.
     */
    public function edit(SuratJalan $suratJalan)
    {
        return view('surat_jalan.edit', [
            'surat' => $suratJalan,
            // Barang lain yang satu surat - dipakai sebagai info di form edit.
            'saudaraSurat' => SuratJalan::query()
                ->where('nomor_surat', $suratJalan->nomor_surat)
                ->where('id', '!=', $suratJalan->getKey())
                ->orderBy('nomor_urut')
                ->orderBy('id')
                ->get(),
        ]);
    }

    /**
     * Nomor urut berikutnya untuk surat yang sama.
     */
    protected function nomorUrutBerikutnya(array $data): int
    {
        $terakhir = SuratJalan::query()
            ->where('nomor_surat', $data['nomor_surat'])
            ->whereDate('tanggal', $data['tanggal'])
            ->max('nomor_urut') ?? 0;

        return (int) $terakhir + 1;
    }

    /**
     * Berapa barang yang kini tercatat dalam satu surat (nomor surat + tanggal).
     */
    protected function jumlahBarangSurat(array $data): int
    {
        return SuratJalan::query()
            ->where('nomor_surat', $data['nomor_surat'])
            ->whereDate('tanggal', $data['tanggal'])
            ->count();
    }

    /**
     * Update satu barang surat jalan.
     */
    public function update(Request $request, SuratJalan $suratJalan)
    {
        $validated = $this->validasi($request);

        // Nomor urut dibiarkan seperti semula kalau form mengosongkannya.
        if (blank($validated['nomor_urut'] ?? null)) {
            unset($validated['nomor_urut']);
        }

        $suratJalan->update($validated);

        return redirect()
            ->route('surat-jalan.index')
            ->with('success', 'Surat jalan berhasil diperbarui.');
    }

    /**
     * Hapus satu barang surat jalan.
     */
    public function destroy(SuratJalan $suratJalan)
    {
        $suratJalan->delete();

        return redirect()
            ->route('surat-jalan.index')
            ->with('success', 'Barang surat jalan berhasil dihapus.');
    }

    /**
     * Aturan validasi yang dipakai store() & update().
     */
    protected function validasi(Request $request): array
    {
        return $request->validate([
            'cabang_area' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'nomor_surat' => 'required|string|max:255',
            'nomor_urut' => 'nullable|integer|min:1|max:9999',
            'kode_barang' => 'nullable|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kemasan_barang' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'asal_penyimpanan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ]);
    }

    /**
     * Bangun data & instance PDF surat jalan sesuai filter aktif.
     *
     * @return array{0: \Barryvdh\DomPDF\PDF, 1: string, 2: array}
     */
    protected function buildPdf(Request $request): array
    {
        $baris = $this->filteredQuery($request)->get();

        $totalBaris = $baris->count();
        $totalJumlah = $baris->sum('jumlah');

        // Di PDF, satu surat jadi satu blok berisi semua barangnya.
        $surat = $this->groupedSurat($baris);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $cabang = $request->input('cabang');

        $namaBulan = $bulan
            ? Carbon::create()->month((int) $bulan)->translatedFormat('F')
            : null;

        $data = compact(
            'baris', 'surat', 'totalBaris', 'totalJumlah',
            'namaBulan', 'tahun', 'cabang'
        );

        $pdf = Pdf::loadView('surat_jalan.pdf', $data)->setPaper('a4', 'landscape');

        // DomPDF menjalankan script isi view dua kali; kalau dibiarkan, baris
        // "HAL. {PAGE_NUM} / {PAGE_COUNT}" di footer ikut tercetak mentah.
        $pdf->setOption('isPhpEnabled', true);

        $namaFile = 'surat-jalan-'
            .($namaBulan ? strtolower($namaBulan).'-'.$tahun : now()->format('Y-m-d'))
            .'.pdf';

        return [$pdf, $namaFile, $data];
    }

    /**
     * Halaman preview PDF.
     */
    public function previewPdf(Request $request)
    {
        [$pdf, $namaFile, $data] = $this->buildPdf($request);

        return view('surat_jalan.pdf_preview', array_merge($data, [
            'namaFile' => $namaFile,
            'streamUrl' => route('surat-jalan.pdf.stream', $request->query()),
            'downloadUrl' => route('surat-jalan.pdf', $request->query()),
        ]));
    }

    /**
     * Stream PDF inline untuk iframe preview.
     */
    public function streamPdf(Request $request)
    {
        [$pdf, $namaFile] = $this->buildPdf($request);

        return $pdf->stream($namaFile);
    }

    /**
     * Download PDF langsung.
     */
    public function exportPdf(Request $request)
    {
        [$pdf, $namaFile] = $this->buildPdf($request);

        return $pdf->download($namaFile);
    }
}
