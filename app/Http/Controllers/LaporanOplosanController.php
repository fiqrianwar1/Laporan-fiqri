<?php

namespace App\Http\Controllers;

use App\Http\Controllers\RiwayatOrderController;
use App\Models\LaporanOplosan;
use App\Support\Cabang;
use App\Support\NotaGrouper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanOplosanController extends Controller
{
    /**
     * Ambil query laporan, difilter bulan, tahun & cabang kalau ada.
     */
    protected function filteredQuery(Request $request)
    {
        $query = LaporanOplosan::query();

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
     * Kelompokkan baris oplosan menjadi nota (1 nota bisa banyak item).
     */
    protected function groupedNotas($laporans)
    {
        return NotaGrouper::withTotals(
            NotaGrouper::group(
                $laporans,
                fn ($laporan) => $laporan->nomor_nota,
                fn ($laporan) => $laporan->no_bukti_nota,
            ),
            fn ($laporan) => (float) $laporan->harga_nota,
        );
    }

    /**
     * Tampilkan semua laporan oplosan + rekap ringkas.
     *
     * Tabelnya dipaginasi PER NOTA supaya item-item dari satu nota tetap
     * tampil dalam satu kartu, tidak terbelah ke halaman berikutnya.
     */
    public function index(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $cabang = $request->input('cabang');

        // Ringkasan dihitung dari seluruh data periode terpilih.
        $semuaLaporan = $this->filteredQuery($request)->get();

        $totalOplosan = $semuaLaporan->count();
        $totalCc = $semuaLaporan->sum('qty_cc');
        $totalBiaya = $semuaLaporan->sum('harga_nota');

        // Ringkasan PER NOTA - jumlah nota & nilai tiap nota dihitung dari
        // item-item yang punya nomor bukti sama.
        $semuaNota = $this->groupedNotas($semuaLaporan);
        $totalNota = $semuaNota->count();
        $totalNilaiNota = $semuaNota->sum('total');
        $rataPerNota = $totalNota > 0 ? $totalNilaiNota / $totalNota : 0;

        // Pecahan per cabang, supaya kelihatan kontribusi tiap cabang.
        $perCabang = $this->ringkasanPerCabang($semuaNota);

        $notas = RiwayatOrderController::paginateNotas($semuaNota, 10, $request);

        return view('laporan_oplosan.index', compact(
            'notas', 'totalOplosan', 'totalCc', 'totalBiaya', 'bulan', 'tahun', 'cabang',
            'totalNota', 'totalNilaiNota', 'rataPerNota', 'perCabang'
        ));
    }

    /**
     * Ringkasan nota per cabang: jumlah nota, item & nilainya.
     */
    protected function ringkasanPerCabang($notas)
    {
        return $notas
            ->groupBy(fn ($nota) => $nota['items']->first()->cabang_area ?: Cabang::default())
            ->map(function ($baris, $nama) {
                return [
                    'cabang' => $nama,
                    'nota'   => $baris->count(),
                    'item'   => $baris->sum(fn ($nota) => $nota['jumlahItem']),
                    'cc'     => $baris->sum(fn ($nota) => $nota['items']->sum('qty_cc')),
                    'nilai'  => $baris->sum('total'),
                ];
            })
            ->sortByDesc('nilai')
            ->values();
    }

    /**
     * Tampilkan form tambah laporan baru.
     */
    public function create()
    {
        return view('laporan_oplosan.create');
    }

    /**
     * Simpan laporan oplosan baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cabang_area' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'no_plat' => 'required|string|max:255',
            'kode_warna_unit' => 'required|string|max:255',
            'no_bukti_nota' => 'required|string|max:255',
            'nomor_urut' => 'nullable|integer|min:1|max:9999',
            'rincian_bahan' => 'required|string|max:255',
            'qty_cc' => 'required|integer|min:1',
            'harga_nota' => 'nullable|numeric|min:0',
            'foto_nota' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('foto_nota')) {
            $validated['foto_nota'] = $request->file('foto_nota')->store('foto-nota', 'public');
        }

        // Kalau nomor urut dikosongkan, item ini ditaruh di urutan terakhir
        // nota tersebut.
        $validated['nomor_urut'] = $validated['nomor_urut'] ?? $this->nomorUrutBerikutnya($validated);

        LaporanOplosan::create($validated);

        $pesan = 'Laporan oplosan berhasil ditambahkan.';
        $jumlahItem = $this->jumlahItemNota($validated);

        if ($jumlahItem > 1) {
            $pesan .= " Nota {$validated['no_bukti_nota']} sekarang berisi {$jumlahItem} item bahan.";
        }

        return redirect()
            ->route('laporan-oplosan.index')
            ->with('success', $pesan);
    }

    /**
     * Tampilkan form edit laporan.
     */
    public function edit(LaporanOplosan $laporanOplosan)
    {
        return view('laporan_oplosan.edit', [
            'laporan' => $laporanOplosan,
            // Item lain yang satu nota - dipakai sebagai info di form edit.
            'saudaraNota' => LaporanOplosan::query()
                ->where('no_bukti_nota', $laporanOplosan->no_bukti_nota)
                ->where('id', '!=', $laporanOplosan->getKey())
                ->orderBy('nomor_urut')
                ->orderBy('id')
                ->get(),
        ]);
    }

    /**
     * Nomor urut berikutnya untuk nota yang sama.
     */
    protected function nomorUrutBerikutnya(array $data): int
    {
        $terakhir = LaporanOplosan::query()
            ->where('no_bukti_nota', $data['no_bukti_nota'])
            ->whereDate('tanggal', $data['tanggal'])
            ->max('nomor_urut') ?? 0;

        return (int) $terakhir + 1;
    }

    /**
     * Berapa item yang kini tercatat dalam satu nota (nomor nota + tanggal).
     */
    protected function jumlahItemNota(array $data): int
    {
        return LaporanOplosan::query()
            ->where('no_bukti_nota', $data['no_bukti_nota'])
            ->whereDate('tanggal', $data['tanggal'])
            ->count();
    }

    /**
     * Update laporan oplosan yang sudah ada.
     */
    public function update(Request $request, LaporanOplosan $laporanOplosan)
    {
        $validated = $request->validate([
            'cabang_area' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'no_plat' => 'required|string|max:255',
            'kode_warna_unit' => 'required|string|max:255',
            'no_bukti_nota' => 'required|string|max:255',
            'nomor_urut' => 'nullable|integer|min:1|max:9999',
            'rincian_bahan' => 'required|string|max:255',
            'qty_cc' => 'required|integer|min:1',
            'harga_nota' => 'nullable|numeric|min:0',
            'foto_nota' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('foto_nota')) {
            if ($laporanOplosan->foto_nota) {
                Storage::disk('public')->delete($laporanOplosan->foto_nota);
            }
            $validated['foto_nota'] = $request->file('foto_nota')->store('foto-nota', 'public');
        }

        // Nomor urut dibiarkan seperti semula kalau form mengosongkannya.
        if (blank($validated['nomor_urut'] ?? null)) {
            unset($validated['nomor_urut']);
        }

        $laporanOplosan->update($validated);

        return redirect()
            ->route('laporan-oplosan.index')
            ->with('success', 'Laporan oplosan berhasil diperbarui.');
    }

    /**
     * Hapus laporan oplosan.
     */
    public function destroy(LaporanOplosan $laporanOplosan)
    {
        if ($laporanOplosan->foto_nota) {
            Storage::disk('public')->delete($laporanOplosan->foto_nota);
        }

        $laporanOplosan->delete();

        return redirect()
            ->route('laporan-oplosan.index')
            ->with('success', 'Laporan oplosan berhasil dihapus.');
    }

    /**
     * Bangun data & instance PDF laporan oplosan sesuai filter aktif.
     *
     * @return array{0: \Barryvdh\DomPDF\PDF, 1: string, 2: array}
     */
    protected function buildPdf(Request $request): array
    {
        $laporans = $this->filteredQuery($request)->get();

        $totalOplosan = $laporans->count();
        $totalCc = $laporans->sum('qty_cc');
        $totalBiaya = $laporans->sum('harga_nota');

        // Di PDF, satu nota jadi satu blok berisi semua itemnya.
        $notas = $this->groupedNotas($laporans);

        // Nota dipisah per cabang supaya Banjarmasin & Palangka tidak tercampur
        // dalam satu deretan, tapi totalnya tetap dijumlah bersama di akhir.
        $bagian = Cabang::kelompokkanNota($notas);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $cabang = $request->input('cabang');

        $namaBulan = $bulan
            ? \Illuminate\Support\Carbon::create()->month((int) $bulan)->translatedFormat('F')
            : null;

        $data = compact(
            'notas', 'bagian', 'totalOplosan', 'totalCc', 'totalBiaya',
            'namaBulan', 'tahun', 'cabang'
        );

        $pdf = Pdf::loadView('laporan_oplosan.pdf', $data)->setPaper('a4', 'landscape');

        $namaFile = 'laporan-oplosan-' . ($namaBulan ? strtolower($namaBulan) . '-' . $tahun : now()->format('Y-m-d')) . '.pdf';

        return [$pdf, $namaFile, $data];
    }

    /**
     * Halaman preview PDF: dokumen langsung tampil di browser,
     * lengkap dengan tombol print & download.
     */
    public function previewPdf(Request $request)
    {
        [$pdf, $namaFile, $data] = $this->buildPdf($request);

        return view('laporan_oplosan.pdf_preview', array_merge($data, [
            'namaFile' => $namaFile,
            'streamUrl' => route('laporan-oplosan.pdf.stream', $request->query()),
            'downloadUrl' => route('laporan-oplosan.pdf', $request->query()),
        ]));
    }

    /**
     * Stream PDF inline (tanpa paksa download) untuk iframe preview.
     */
    public function streamPdf(Request $request)
    {
        [$pdf, $namaFile] = $this->buildPdf($request);

        return $pdf->stream($namaFile);
    }

    /**
     * Export laporan oplosan (sesuai filter yang aktif) jadi PDF (langsung download).
     */
    public function exportPdf(Request $request)
    {
        [$pdf, $namaFile] = $this->buildPdf($request);

        return $pdf->download($namaFile);
    }
}
