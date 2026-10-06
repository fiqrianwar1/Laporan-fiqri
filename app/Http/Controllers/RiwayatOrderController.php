<?php

namespace App\Http\Controllers;

use App\Models\RiwayatOrder;
use App\Support\Cabang;
use App\Support\NotaGrouper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RiwayatOrderController extends Controller
{
    /**
     * Ambil query order, difilter bulan, tahun & cabang kalau ada.
     */
    protected function filteredQuery(Request $request)
    {
        $query = RiwayatOrder::query();

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->input('bulan'));
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->input('tahun'));
        }
        if ($request->filled('cabang')) {
            $query->where('cabang_area', $request->input('cabang'));
        }

        // Nomor urut dijadikan urutan kedua supaya item dalam satu nota
        // tetap berurutan sesuai nota aslinya.
        return $query->orderBy('tanggal')->orderBy('nomor_urut')->orderBy('id');
    }

    /**
     * Kelompokkan baris order menjadi nota (1 nota bisa banyak item).
     */
    protected function groupedNotas($orders)
    {
        return NotaGrouper::withTotals(
            NotaGrouper::group(
                $orders,
                fn ($order) => $order->nomor_bukti,
                fn ($order) => $order->no_bukti_faktur,
            ),
            fn ($order) => (float) $order->total_item,
        );
    }

    /**
     * Tampilkan semua riwayat order + total belanja.
     *
     * Tabelnya dipaginasi PER NOTA (bukan per baris) supaya satu nota
     * dengan banyak item tidak terbelah ke halaman berikutnya.
     */
    public function index(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $cabang = $request->input('cabang');

        // Ringkasan dihitung dari seluruh data periode terpilih.
        $semuaOrder = $this->filteredQuery($request)->get();

        $totalBelanja = $semuaOrder->sum(fn ($order) => (float) $order->total_item);
        $totalItem = $semuaOrder->sum('qty');

        // Ringkasan PER NOTA - jumlah nota & nilai tiap nota dihitung dari
        // item-item yang punya nomor bukti sama.
        $semuaNota = $this->groupedNotas($semuaOrder);
        $totalNota = $semuaNota->count();
        $totalNilaiNota = $semuaNota->sum('total');
        $rataPerNota = $totalNota > 0 ? $totalNilaiNota / $totalNota : 0;

        // Pecahan per cabang, supaya kelihatan kontribusi tiap cabang.
        $perCabang = $this->ringkasanPerCabang($semuaNota);

        $notas = $this->paginateNotas($semuaNota, 10, $request);

        return view('riwayat_order.index', compact(
            'notas', 'totalBelanja', 'totalItem', 'bulan', 'tahun', 'cabang',
            'totalNota', 'totalNilaiNota', 'rataPerNota', 'perCabang'
        ));
    }

    /**
     * Ringkasan nota per cabang: jumlah nota, jumlah item & nilainya.
     */
    protected function ringkasanPerCabang($notas)
    {
        return $notas
            ->groupBy(fn ($nota) => $nota['items']->first()->cabang_area ?: Cabang::default())
            ->map(function ($baris, $nama) {
                return [
                    'cabang'   => $nama,
                    'nota'     => $baris->count(),
                    'item'     => $baris->sum(fn ($nota) => $nota['jumlahItem']),
                    'nilai'    => $baris->sum('total'),
                ];
            })
            ->sortByDesc('nilai')
            ->values();
    }

    /**
     * Paginasi manual untuk kumpulan nota (Collection).
     * Dipakai bersama oleh halaman tinter & manajer.
     */
    public static function paginateNotas($notas, int $perHalaman, Request $request)
    {
        $halaman = max(1, (int) $request->input('page', 1));
        $total = $notas->count();
        $potongan = $notas->slice(($halaman - 1) * $perHalaman, $perHalaman)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $potongan,
            $total,
            $perHalaman,
            $halaman,
            ['path' => $request->url(), 'query' => $request->query()],
        );
    }

    /**
     * Tampilkan form tambah order baru.
     */
    public function create()
    {
        return view('riwayat_order.create');
    }

    /**
     * Simpan riwayat order baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cabang_area' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'no_bukti_faktur' => 'required|string|max:255',
            'nomor_urut' => 'nullable|integer|min:1|max:9999',
            'kode_barang' => 'nullable|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'harga_satuan' => 'required|numeric|min:0',
            'diskon_persen' => 'nullable|numeric|min:0|max:100',
            'foto_faktur' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('foto_faktur')) {
            $validated['foto_faktur'] = $request->file('foto_faktur')->store('foto-faktur', 'public');
        }

        // Kalau nomor urut dikosongkan, item ini otomatis ditaruh di urutan
        // terakhir nota tersebut.
        $validated['nomor_urut'] = $validated['nomor_urut'] ?? $this->nomorUrutBerikutnya($validated);

        RiwayatOrder::create($validated);

        $pesan = 'Riwayat order berhasil ditambahkan.';
        $jumlahItem = $this->jumlahItemNota($validated);

        if ($jumlahItem > 1) {
            $pesan .= " Nota {$validated['no_bukti_faktur']} sekarang berisi {$jumlahItem} item barang.";
        }

        return redirect()
            ->route('riwayat-order.index')
            ->with('success', $pesan);
    }

    /**
     * Tampilkan form edit order.
     */
    public function edit(RiwayatOrder $riwayatOrder)
    {
        return view('riwayat_order.edit', [
            'order' => $riwayatOrder,
            // Item lain yang berada di nota yang sama - ditampilkan di form
            // edit supaya jelas nota ini isinya berapa barang.
            'saudaraNota' => RiwayatOrder::query()
                ->where('no_bukti_faktur', $riwayatOrder->no_bukti_faktur)
                ->where('id', '!=', $riwayatOrder->getKey())
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
        $terakhir = RiwayatOrder::query()
            ->where('no_bukti_faktur', $data['no_bukti_faktur'])
            ->whereDate('tanggal', $data['tanggal'])
            ->max('nomor_urut') ?? 0;

        return (int) $terakhir + 1;
    }

    /**
     * Berapa item yang kini tercatat dalam satu nota (nomor bukti + tanggal).
     */
    protected function jumlahItemNota(array $data): int
    {
        return RiwayatOrder::query()
            ->where('no_bukti_faktur', $data['no_bukti_faktur'])
            ->whereDate('tanggal', $data['tanggal'])
            ->count();
    }

    /**
     * Update riwayat order yang sudah ada.
     */
    public function update(Request $request, RiwayatOrder $riwayatOrder)
    {
        $validated = $request->validate([
            'cabang_area' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'no_bukti_faktur' => 'required|string|max:255',
            'nomor_urut' => 'nullable|integer|min:1|max:9999',
            'kode_barang' => 'nullable|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'harga_satuan' => 'required|numeric|min:0',
            'diskon_persen' => 'nullable|numeric|min:0|max:100',
            'foto_faktur' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('foto_faktur')) {
            if ($riwayatOrder->foto_faktur) {
                Storage::disk('public')->delete($riwayatOrder->foto_faktur);
            }
            $validated['foto_faktur'] = $request->file('foto_faktur')->store('foto-faktur', 'public');
        }

        // Nomor urut dibiarkan seperti semula kalau form tidak mengirim nilainya,
        // supaya item ini tidak berpindah posisi tanpa sengaja.
        if (blank($validated['nomor_urut'] ?? null)) {
            unset($validated['nomor_urut']);
        }

        $riwayatOrder->update($validated);

        return redirect()
            ->route('riwayat-order.index')
            ->with('success', 'Riwayat order berhasil diperbarui.');
    }

    /**
     * Hapus riwayat order.
     */
    public function destroy(RiwayatOrder $riwayatOrder)
    {
        if ($riwayatOrder->foto_faktur) {
            Storage::disk('public')->delete($riwayatOrder->foto_faktur);
        }

        $riwayatOrder->delete();

        return redirect()
            ->route('riwayat-order.index')
            ->with('success', 'Riwayat order berhasil dihapus.');
    }

    /**
     * Bangun data & instance PDF riwayat order sesuai filter aktif.
     *
     * @return array{0: \Barryvdh\DomPDF\PDF, 1: string, 2: array}
     */
    protected function buildPdf(Request $request): array
    {
        $orders = $this->filteredQuery($request)->get();

        $totalBelanja = $orders->sum(fn ($order) => (float) $order->total_item);

        // Di PDF, satu nota ditampilkan sebagai satu blok berisi semua
        // itemnya supaya nota tidak terpecah dan mudah dicocokkan dengan faktur.
        $notas = $this->groupedNotas($orders);

        // Nota dipisah per cabang supaya Banjarmasin & Palangka tidak tercampur
        // dalam satu deretan, tapi totalnya tetap dijumlah bersama di akhir.
        $bagian = Cabang::kelompokkanNota($notas);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $cabang = $request->input('cabang');

        $namaBulan = $bulan
            ? \Illuminate\Support\Carbon::create()->month((int) $bulan)->translatedFormat('F')
            : null;

        // Halaman preview butuh daftar transaksi mentah untuk ringkasan
        // (jumlah transaksi & total qty), jadi $orders ikut dikirim.
        $data = compact(
            'notas', 'bagian', 'orders', 'totalBelanja',
            'namaBulan', 'tahun', 'cabang'
        );

        $pdf = Pdf::loadView('riwayat_order.pdf', $data)->setPaper('a4', 'landscape');

        // DomPDF menjalankan script isi view dua kali; kalau dibiarkan, baris
        // "HAL. {PAGE_NUM} / {PAGE_COUNT}" di footer ikut tercetak mentah.
        $pdf->setOption('isPhpEnabled', true);

        $namaFile = 'riwayat-order-' . ($namaBulan ? strtolower($namaBulan) . '-' . $tahun : now()->format('Y-m-d')) . '.pdf';

        return [$pdf, $namaFile, $data];
    }

    /**
     * Halaman preview PDF: dokumen langsung tampil di browser,
     * lengkap dengan tombol print & download.
     */
    public function previewPdf(Request $request)
    {
        [$pdf, $namaFile, $data] = $this->buildPdf($request);

        return view('riwayat_order.pdf_preview', array_merge($data, [
            'namaFile' => $namaFile,
            'streamUrl' => route('riwayat-order.pdf.stream', $request->query()),
            'downloadUrl' => route('riwayat-order.pdf', $request->query()),
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
     * Export riwayat order (sesuai filter yang aktif) jadi PDF (langsung download).
     */
    public function exportPdf(Request $request)
    {
        [$pdf, $namaFile] = $this->buildPdf($request);

        return $pdf->download($namaFile);
    }
}
