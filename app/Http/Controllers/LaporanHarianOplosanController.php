<?php

namespace App\Http\Controllers;

use App\Http\Controllers\RiwayatOrderController;
use App\Models\LaporanHarianOplosan;
use App\Support\Cabang;
use App\Support\HarianGrouper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Laporan harian oplosan cat (paint mixing / tinter).
 *
 * Berbeda dari LaporanOplosanController: di sini data tidak dikelompokkan
 * per nota. Satu baris tabel = satu pekerjaan oplosan, jadi rekapnya
 * persis seperti form harian tim body & paint.
 */
class LaporanHarianOplosanController extends Controller
{
    /**
     * Daftar pilihan hasil pencocokan warna.
     *
     * @return array<int, string>
     */
    public static function pilihanMatching(): array
    {
        return ['Sama', 'Mirip', 'Beda'];
    }

    /**
     * Daftar bahan cat yang sering dipakai (dipakai sebagai datalist
     * bantuan isian, bukan pilihan wajib - teks bebas tetap boleh).
     *
     * @return array<int, string>
     */
    public static function pilihanBahan(): array
    {
        return ['Autobase', 'Autocryl', 'Wanda SB', 'Wanda 2K'];
    }

    /**
     * Ambil query laporan, difilter bulan, tahun & cabang kalau ada.
     */
    protected function filteredQuery(Request $request)
    {
        $query = LaporanHarianOplosan::query();

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->input('bulan'));
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->input('tahun'));
        }
        if ($request->filled('cabang')) {
            $query->where('cabang_area', $request->input('cabang'));
        }

        return $query->orderBy('tanggal')->orderBy('jam_dibuat')->orderBy('id');
    }

    /**
     * Jumlah hari per halaman di daftar laporan harian.
     *
     * Satu halaman = 10 tanggal, tiap tanggal memuat semua pekerjaan hari itu
     * dalam satu kartu. Dipaginasi PER HARI supaya pekerjaan satu tanggal tidak
     * terbelah ke halaman berikutnya - sama seperti daftar oplosan yang
     * dipaginasi per nota.
     */
    public const HARI_PER_HALAMAN = 10;

    /**
     * Kelompokkan baris laporan harian menjadi blok per tanggal.
     */
    protected function groupedHari($baris)
    {
        return HarianGrouper::group($baris);
    }

    /**
     * Tampilkan rekap harian oplosan + ringkasan.
     */
    public function index(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $cabang = $request->input('cabang');

        $semua = $this->filteredQuery($request)->get();

        // Daftar ditampilkan sebagai blok per hari, jadi paginasi pun per hari -
        // 10 tanggal per halaman, bukan 10 baris. Ringkasan tetap dihitung dari
        // seluruh data periode terpilih (bukan hanya halaman yang tampil).
        $semuaHari = $this->groupedHari($semua);
        $hariHalaman = RiwayatOrderController::paginateNotas(
            $semuaHari,
            self::HARI_PER_HALAMAN,
            $request,
        );

        $totalBaris = $semua->count();
        $totalVolume = $semua->sum('volume_cc');
        $totalDurasi = $semua->sum('durasi_menit');
        $jumlahSama = $semua->where('hasil_matching', 'Sama')->count();
        $persenSama = $totalBaris > 0 ? round(($jumlahSama / $totalBaris) * 100) : 0;

        // Ringkasan per cabang supaya kelihatan kontribusi tiap area.
        // 'persen' = porsi volume cabang terhadap total volume periode ini,
        // dipakai untuk progress bar di kartu slider halaman index.
        $totalVolumeAman = $totalVolume > 0 ? $totalVolume : 1;
        $perCabang = $semua
            ->groupBy(fn ($baris) => $baris->cabang_area ?: Cabang::default())
            ->map(fn ($baris, $nama) => [
                'cabang' => $nama,
                'baris' => $baris->count(),
                'volume' => $baris->sum('volume_cc'),
                'sama' => $baris->where('hasil_matching', 'Sama')->count(),
                'persen' => round(($baris->sum('volume_cc') / $totalVolumeAman) * 100),
            ])
            ->sortByDesc('volume')
            ->values();

        // Rekap berapa kali tiap bahan cat dipakai.
        $perBahan = $semua
            ->groupBy('bahan_cat')
            ->map(fn ($baris, $nama) => [
                'bahan' => $nama,
                'jumlah' => $baris->count(),
                'volume' => $baris->sum('volume_cc'),
            ])
            ->sortByDesc('volume')
            ->take(6)
            ->values();

        return view('laporan_harian.index', compact(
            'hariHalaman', 'totalBaris', 'totalVolume', 'totalDurasi', 'persenSama',
            'perCabang', 'perBahan', 'bulan', 'tahun', 'cabang'
        ));
    }

    /**
     * Tampilkan form tambah laporan harian.
     */
    public function create()
    {
        return view('laporan_harian.create', [
            'pilihanMatching' => self::pilihanMatching(),
            'pilihanBahan' => self::pilihanBahan(),
        ]);
    }

    /**
     * Simpan satu baris laporan harian baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validasi($request);
        $validated['volume_cc'] = blank($validated['volume_cc'] ?? null) ? 0 : (int) $validated['volume_cc'];

        if (blank($validated['durasi_menit'] ?? null)) {
            $validated['durasi_menit'] = LaporanHarianOplosan::hitungDurasi(
                $validated['jam_dibuat'] ?? null,
                $validated['jam_selesai'] ?? null,
            );
        }

        LaporanHarianOplosan::create($validated);

        return redirect()
            ->route('laporan-harian.index')
            ->with('success', 'Baris laporan harian berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit.
     */
    public function edit(LaporanHarianOplosan $laporanHarian)
    {
        return view('laporan_harian.edit', [
            'baris' => $laporanHarian,
            'pilihanMatching' => self::pilihanMatching(),
            'pilihanBahan' => self::pilihanBahan(),
        ]);
    }

    /**
     * Update satu baris laporan harian.
     */
    public function update(Request $request, LaporanHarianOplosan $laporanHarian)
    {
        $validated = $this->validasi($request);
        $validated['volume_cc'] = blank($validated['volume_cc'] ?? null) ? 0 : (int) $validated['volume_cc'];

        if (blank($validated['durasi_menit'] ?? null)) {
            $validated['durasi_menit'] = LaporanHarianOplosan::hitungDurasi(
                $validated['jam_dibuat'] ?? null,
                $validated['jam_selesai'] ?? null,
            );
        }

        $laporanHarian->update($validated);

        return redirect()
            ->route('laporan-harian.index')
            ->with('success', 'Baris laporan harian berhasil diperbarui.');
    }

    /**
     * Hapus satu baris laporan harian.
     */
    public function destroy(LaporanHarianOplosan $laporanHarian)
    {
        $laporanHarian->delete();

        return redirect()
            ->route('laporan-harian.index')
            ->with('success', 'Baris laporan harian berhasil dihapus.');
    }

    /**
     * Aturan validasi yang dipakai store() & update().
     */
    protected function validasi(Request $request): array
    {
        return $request->validate([
            'cabang_area' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'plat_nomor' => 'required|string|max:255',
            'kode_warna' => 'required|string|max:255',
            'tipe_mobil' => 'required|string|max:255',
            'bahan_cat' => 'required|string|max:255',
            // Volume boleh 0 / dikosongkan untuk pemakaian cat sisa, jadi
            // tidak diwajibkan; nilai kosong dinormalkan jadi 0 di bawah.
            'volume_cc' => 'nullable|integer|min:0',
            'jam_dibuat' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'durasi_menit' => 'nullable|integer|min:0|max:1440',
            'hasil_matching' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ]);
    }

    /**
     * Bangun data & instance PDF sesuai filter aktif.
     *
     * @return array{0: \Barryvdh\DomPDF\PDF, 1: string, 2: array}
     */
    protected function buildPdf(Request $request): array
    {
        $baris = $this->filteredQuery($request)->get();

        // Di PDF, satu tanggal jadi satu blok berisi semua pekerjaan hari itu,
        // jadi tidak ada lagi pekerjaan yang tercetak terpisah-pisah.
        $hari = $this->groupedHari($baris);

        $totalBaris = $baris->count();
        $totalVolume = $baris->sum('volume_cc');
        $totalDurasi = $baris->sum('durasi_menit');
        $jumlahSama = $baris->where('hasil_matching', 'Sama')->count();

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $cabang = $request->input('cabang');

        $namaBulan = $bulan
            ? Carbon::create()->month((int) $bulan)->translatedFormat('F')
            : null;

        $data = compact(
            'baris', 'hari', 'totalBaris', 'totalVolume', 'totalDurasi', 'jumlahSama',
            'namaBulan', 'tahun', 'cabang'
        );

        $pdf = Pdf::loadView('laporan_harian.pdf', $data)->setPaper('a4', 'landscape');

        // DomPDF menjalankan script isi view dua kali; kalau dibiarkan, baris
        // "HAL. {PAGE_NUM} / {PAGE_COUNT}" di footer ikut tercetak mentah.
        $pdf->setOption('isPhpEnabled', true);

        $namaFile = 'laporan-harian-oplosan-'
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

        return view('laporan_harian.pdf_preview', array_merge($data, [
            'namaFile' => $namaFile,
            'streamUrl' => route('laporan-harian.pdf.stream', $request->query()),
            'downloadUrl' => route('laporan-harian.pdf', $request->query()),
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
