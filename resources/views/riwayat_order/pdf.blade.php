<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Order Barang</title>
    <style>
        /* Nomor halaman di footer ditulis lewat script php, karena DomPDF
           tidak mendukung at-rule @page { @bottom-center }. */
        @page { margin: 0 26px 36px; }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #1e293b;
            background: #fff;
        }

        .banner {
            background: #1d4ed8;
            background-image: linear-gradient(120deg, #1e40af 0%, #1d4ed8 55%, #2563eb 100%);
            color: #fff;
            padding: 16px 26px 15px;
            margin: 0 -26px 0;
        }

        .banner table { width: 100%; border-collapse: collapse; }
        .banner td { vertical-align: middle; padding: 0; }

        /* Kotak logo WTJ. display: block wajib supaya width/height dipakai
           DomPDF — sebagai span (inline) kotaknya jadi pil tinggi yang
           menyembul keluar banner. */
        .logo {
            display: block;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #ffffff;
            text-align: center;
            padding: 3px;
        }

        .logo img {
            width: 38px;
            height: 38px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            border-radius: 7px;
        }

        .logo .inisial {
            display: block;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 700;
            line-height: 38px;
            letter-spacing: 0;
        }

        .banner .brand {
            font-size: 10px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #bfdbfe;
            margin: 0 0 3px;
            font-weight: 700;
        }

        .banner .title {
            font-size: 19px;
            font-weight: 700;
            margin: 0 0 3px;
        }

        .banner .subtitle {
            font-size: 9px;
            color: #dbeafe;
            margin: 0;
        }

        /* Badge periode di sisi kanan banner */
        .banner .periode {
            text-align: right;
            font-size: 8px;
            color: #bfdbfe;
            line-height: 1.5;
            white-space: nowrap;
        }

        .banner .periode strong {
            display: block;
            font-size: 12px;
            color: #fff;
            letter-spacing: .3px;
        }

        .banner-accent {
            height: 5px;
            background-image: linear-gradient(90deg, #f59e0b 0%, #f59e0b 35%, #fbbf24 100%);
            margin: 0 -26px 16px;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .meta td {
            padding: 0;
            font-size: 8.5px;
            color: #475569;
        }

        .meta .badge {
            display: inline-block;
            background: #eef2ff;
            color: #1d4ed8;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 10px;
            font-size: 8px;
            margin-right: 5px;
        }

        .meta .badge.kosong { background: #f1f5f9; color: #475569; margin-right: 0; }

        .meta .right { text-align: right; color: #94a3b8; }

        .summary {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin: 0 -10px 16px;
        }

        .summary td {
            border-radius: 8px;
            padding: 11px 14px 12px;
            width: 33%;
            border-top: 3px solid #cbd5e1;
        }

        .summary .card-blue { background: #eff6ff; border: 1px solid #bfdbfe; border-top: 3px solid #2563eb; }
        .summary .card-amber { background: #fffbeb; border: 1px solid #fde68a; border-top: 3px solid #d97706; }
        .summary .card-green { background: #ecfdf5; border: 1px solid #a7f3d0; border-top: 3px solid #059669; }

        .icon-badge {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
            line-height: 20px;
            margin-bottom: 6px;
        }

        .icon-blue { background: #2563eb; }
        .icon-amber { background: #d97706; }
        .icon-green { background: #059669; }

        .summary .label {
            display: block;
            text-transform: uppercase;
            font-size: 7px;
            font-weight: 700;
            color: #64748b;
            letter-spacing: .5px;
            margin-bottom: 2px;
        }

        .summary .value {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        table.data th,
        table.data td {
            border: 1px solid #d7dee8;
            padding: 5px 6px;
            vertical-align: middle;
        }

        table.data th {
            background: #1d4ed8;
            color: #fff;
            text-transform: uppercase;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: .25px;
        }

        table.data td { font-size: 8px; }

        table.data tbody tr:nth-child(even) td { background: #f5f8ff; }

        .col-no   { width: 5%;  }
        .col-qty  { width: 7%;  }
        .col-uang { width: 13%; }

        .right { text-align: right; }
        .center { text-align: center; }
        .strong { font-weight: 700; color: #0f172a; }
        .muted { color: #64748b; }

        .total-row td {
            background: #fffbeb !important;
            border-top: 2px solid #f59e0b;
            font-weight: 700;
            color: #92400e;
            font-size: 8.5px;
        }

        /* Blok per nota: judul nota + tabel item di bawahnya. */
        .nota {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .nota-head {
            background: #eef2ff;
            border-bottom: 1px solid #c7d2fe;
            padding: 6px 9px;
        }

        /* Kepala nota dibuat sebagai tabel supaya nama barang panjang bisa
           turun ke baris berikutnya, tidak menabrak angka totalnya. */
        table.nota-head { width: 100%; border-collapse: collapse; }
        table.nota-head td { vertical-align: top; padding: 0; }

        .nota-head .nomor {
            font-size: 9.5px;
            font-weight: 700;
            color: #1e3a8a;
        }

        .nota-head .info {
            font-size: 7.5px;
            color: #475569;
            margin-top: 1px;
        }

        .nota-head .total {
            text-align: right;
            white-space: nowrap;
            width: 120px;
        }

        .nota-head .total .lbl {
            display: block;
            font-size: 6.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .25px;
            color: #94a3b8;
        }

        .nota-head .total .angka {
            font-size: 10.5px;
            font-weight: 700;
            color: #0f172a;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
        }

        table.items th {
            background: #f1f5f9;
            color: #334155;
            text-transform: uppercase;
            font-size: 6.5px;
            font-weight: 700;
            letter-spacing: .25px;
            padding: 4px 6px;
            border-bottom: 1px solid #cbd5e1;
        }

        table.items td {
            font-size: 8.4px;
            padding: 5.5px 7px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: middle;
        }

        table.items tbody tr:nth-child(even) td { background: #fafcff; }

        table.items tr:last-child td { border-bottom: 0; }

        /* Baris potongan diskon per nota: penanda bahwa total item sudah
           dipotong, jadi angkanya tidak terlihat "beda sendiri". */
        table.items tfoot td {
            background: #fff7ed;
            border-top: 1px solid #fed7aa;
            font-size: 7.5px;
            color: #b45309;
            padding: 4px 6px;
        }

        /* Bukti foto faktur. Semua thumbnail dipaksa 54x54px dengan object-fit
           cover, jadi foto apapun resolusinya tampil sama besar dan blok nota
           tidak ikut memanjang. */
        .nota-foto {
            padding: 5px 6px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .nota-foto img {
            width: 54px;
            height: 54px;
            object-fit: cover;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            margin-right: 5px;
        }

        .nota-foto .label {
            font-size: 6.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .25px;
            color: #64748b;
            margin-right: 6px;
        }

        .item-no {
            color: #94a3b8;
            font-weight: 700;
        }

        .sign {
            margin-top: 22px;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
        }

        .sign td {
            width: 50%;
            text-align: center;
            font-size: 8.5px;
            color: #475569;
            vertical-align: top;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 8px 14px;
        }

        .sign-space { height: 30px; }

        .sign-name {
            color: #0f172a;
            font-weight: 700;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            display: inline-block;
            min-width: 140px;
        }

        /* Teks footer: rata kiri, satu bagian, karena nomor halamannya
           ditulis terpisah oleh script DomPDF di posisi tetap. */
        .footer {
            position: fixed;
            bottom: -26px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5px;
            color: #94a3b8;
        }

        /* Baris tanda tangan tidak boleh kepotong ke halaman berikutnya. */
        .sign,
        .grand { page-break-inside: avoid; }

        /* ===== Pemisah cabang =====
           Nota tiap cabang dikelompokkan di bawah judul "Bagian" sendiri,
           jadi Banjarmasin & Palangka tidak tercampur dalam satu deretan. */
        .bagian {
            margin: 14px 0 9px;
            border-left: 4px solid #1d4ed8;
            background: #f1f5f9;
            padding: 7px 12px;
            page-break-after: avoid;
        }

        .bagian .nama {
            font-size: 11.5px;
            font-weight: 700;
            color: #1e3a8a;
            /* Tanpa letter-spacing: huruf terakhir ("Palangka Raya") sempat
               terpotong saat dicetak DomPDF. */
            letter-spacing: 0;
        }

        .bagian .ket {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 1px;
        }

        .bagian .nilai {
            float: right;
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Jumlah per cabang, muncul di akhir tiap bagian. */
        /* Jumlah per cabang, muncul di akhir tiap bagian. Warnanya sama dengan
           blok nota di atasnya supaya terbaca sebagai satu kesatuan. */
        .subtotal td {
            background: #eef2ff !important;
            font-weight: 700;
            color: #1e293b;
            font-size: 8.5px;
            padding: 6px;
            border-top: 2px solid #1d4ed8;
            border-bottom: 1px solid #d7dee8;
        }

        /* Kotak total keseluruhan (gabungan semua cabang) - dibuat meniru
           bagian bawah faktur: baris total, diskon, lalu grand total. */
        .grand {
            margin-top: 14px;
            border: 2px solid #1d4ed8;
            border-radius: 8px;
            background: #eff6ff;
            padding: 10px 14px;
        }

        .grand .label {
            display: block;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #1e40af;
        }

        .grand .angka {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }

        .grand .rinci {
            font-size: 7.5px;
            color: #475569;
            margin-top: 2px;
        }

        /* Baris Total / Diskon / Grand Total di dalam kotak total.
           Angkanya rata kanan supaya bisa dicocokkan dengan faktur. */
        /* Blok Total / Diskon / Grand Total: label di kiri, angka rata kanan
           dengan lebar tetap supaya digit satuannya sejajar lurus. */
        table.hitung {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.hitung td {
            font-size: 8.5px;
            padding: 3px 0;
            vertical-align: middle;
        }

        table.hitung td.angka {
            text-align: right;
            width: 130px;
            white-space: nowrap;
        }

        table.hitung tr.rumus td {
            border-top: 1px dashed #93c5fd;
        }

        table.hitung tr.grand-total td {
            border-top: 2px solid #1d4ed8;
            padding-top: 5px;
            font-size: 10.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .nomor-bagian {
            display: inline-block;
            background: #1d4ed8;
            color: #fff;
            font-weight: 700;
            font-size: 7.5px;
            padding: 1px 6px;
            border-radius: 8px;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="banner">
        <table>
            <tr>
                <td style="width: 54px;">
                    @php $logo = \App\Support\Logo::path(); @endphp
                    <span class="logo">
                        @if ($logo)
                            <img src="{{ $logo }}" alt="Logo WTJ">
                        @else
                            <span class="inisial">WTJ</span>
                        @endif
                    </span>
                </td>
                <td>
                    <p class="brand">PT. Warna Tanjung Jaya</p>
                    <p class="title">Riwayat Order Barang</p>
                    <p class="subtitle">Suplai Bahan / Consumable ke Wira Toyota Banjarmasin</p>
                </td>
                <td class="periode" style="width: 130px;">
                    Periode Laporan
                    <strong>
                        @if ($namaBulan)
                            {{ $namaBulan }} {{ $tahun }}
                        @else
                            Semua Bulan {{ $tahun }}
                        @endif
                    </strong>
                </td>
            </tr>
        </table>
    </div>
    <div class="banner-accent"></div>

    <table class="meta">
        <tr>
            <td>
                @php
                    // Nomor dokumen memuat kode cabang kalau PDF-nya difilter
                    // satu cabang. Kalau semua cabang, kodenya ditulis ALL
                    // karena dokumen ini mencakup keduanya.
                    $kodeDokumen = $cabang ? \App\Support\Cabang::singkatan($cabang) : 'ALL';
                @endphp
                <span class="badge">No. Dokumen: RO-{{ $kodeDokumen }}-{{ $tahun }}{{ $namaBulan ? '-' . strtoupper(substr($namaBulan, 0, 3)) : '' }}</span>
                <span class="badge kosong">{{ $cabang ?: 'Seluruh Cabang' }}</span>
            </td>
            <td class="right">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td class="card-blue">
                <span class="icon-badge icon-blue">&#8801;</span>
                <span class="label">Jumlah Nota / Item</span>
                <span class="value">{{ $notas->count() }} nota &middot; {{ number_format($notas->sum(fn ($n) => $n['items']->sum('qty'))) }} item</span>
            </td>
            <td class="card-amber">
                <span class="icon-badge icon-amber">&#931;</span>
                <span class="label">Total Qty Barang</span>
                <span class="value">{{ number_format($notas->sum(fn ($n) => $n['items']->sum('qty'))) }}</span>
            </td>
            <td class="card-green">
                <span class="icon-badge icon-green">Rp</span>
                <span class="label">Total Belanja</span>
                <span class="value">{{ \App\Support\Rupiah::format($totalBelanja) }}</span>
            </td>
        </tr>
    </table>

    @forelse ($bagian as $grup)
        {{-- Judul bagian cabang: nota di bawahnya bernomor ulang dari 1. --}}
        <div class="bagian">
            <span class="nilai">{{ \App\Support\Rupiah::format($grup['notas']->sum('total')) }}</span>
            <span class="nama">Bagian {{ $loop->iteration }} &mdash; {{ $grup['cabang'] }}</span>
            <div class="ket">
                {{ $grup['notas']->count() }} nota &middot;
                {{ number_format($grup['notas']->sum(fn ($n) => $n['items']->sum('qty'))) }} item &middot;
                Kode: {{ $grup['kode'] }}
            </div>
        </div>

        @foreach ($grup['notas'] as $nota)
        @php
            // Foto bukti faktur. Tiap item sering di-upload foto yang isinya sama,
            // jadi disaring pakai isi filenya dulu, baru disematkan sebagai base64
            // supaya DomPDF tidak perlu mengambil file dari URL.
            $fotoNota = \App\Support\FotoNota::unik($nota['items'], 'foto_faktur', 6)
                ->map(function ($path) {
                    $lengkap = storage_path('app/public/' . $path);

                    return is_file($lengkap)
                        ? 'data:image/' . strtolower(pathinfo($lengkap, PATHINFO_EXTENSION))
                            . ';base64,' . base64_encode(file_get_contents($lengkap))
                        : null;
                })
                ->filter()
                ->values();
        @endphp
        {{-- Satu nota = satu blok; semua itemnya kelihatan,
             jadi faktur bisa dicocokkan langsung dengan daftarnya. --}}
        <div class="nota">
            <div class="nota-head">
                <table class="nota-head">
                    <tr>
                        <td>
                            <span class="nomor">
                                <span class="nomor-bagian">Nota #{{ $nota['nomorBagian'] }}</span>
                                {{ \App\Support\Tanggal::panjang($nota['tanggal']) }}
                            </span>
                            <div class="info">
                                {{ $nota['nomor'] ?? 'Tanpa nomor' }} &middot;
                                {{ $nota['items']->count() }} item &middot;
                                {{ number_format($nota['items']->sum('qty')) }} qty setelah diskon
                            </div>
                        </td>
                        <td class="total">
                            <span class="lbl">Total Nota</span>
                            <span class="angka">{{ \App\Support\Rupiah::format($nota['total']) }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            @if ($fotoNota->isNotEmpty())
                <div class="nota-foto">
                    <span class="label">Bukti Foto</span><img src="{{ $fotoNota->first() }}" alt="Foto faktur">
                    @foreach ($fotoNota->slice(1) as $gambar)
                        <img src="{{ $gambar }}" alt="Foto faktur">
                    @endforeach
                </div>
            @endif

            <table class="items">
                <thead>
                    <tr>
                        <th class="center col-no">No</th>
                        <th style="width: 10%;">Kode</th>
                        <th>Nama Barang</th>
                        <th class="right col-qty">Qty</th>
                        <th style="width: 8%;">Satuan</th>
                        <th class="right" style="width: 12%;">Harga Satuan</th>
                        <th class="right" style="width: 8%;">Diskon</th>
                        <th class="right" style="width: 11%;">Nominal Diskon</th>
                        <th class="right" style="width: 14%;">Total Item</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nota['items'] as $item)
                        <tr>
                            <td class="center item-no">{{ $item->nomor_urut ?? $loop->iteration }}</td>
                            <td class="muted">{{ $item->kode_barang ?? '-' }}</td>
                            <td class="strong">{{ $item->nama_barang }}</td>
                            <td class="right">{{ number_format($item->qty) }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td class="right">{{ \App\Support\Rupiah::format($item->harga_satuan) }}</td>
                            <td class="right">{{ number_format($item->diskon_persen, 2, ',', '.') }}%</td>
                            <td class="right muted">
                                {{ \App\Support\Rupiah::formatRingkas($item->nominal_diskon) }}
                            </td>
                            <td class="right strong">{{ \App\Support\Rupiah::format($item->total_item) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                @php $diskonNota = $nota['items']->sum(fn ($item) => (float) $item->nominal_diskon); @endphp
                @if ($diskonNota > 0)
                    {{-- Baris ini yang bikin total kelihatan cocok: nominal
                         sebelum diskon dikurangi potongan = total nota. --}}
                    <tfoot>
                        <tr class="potongan">
                            <td colspan="7" class="right">
                                Potongan diskon nota ini:
                                {{ \App\Support\Rupiah::format($nota['items']->sum(fn ($item) => (float) $item->total_kotor)) }}
                                &minus; {{ \App\Support\Rupiah::format($diskonNota) }}
                            </td>
                            <td class="right strong">&minus; {{ \App\Support\Rupiah::format($diskonNota) }}</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        {{-- Subtotal cabang ini, langsung di bawah notanya yang terakhir. --}}
        <table class="data">
            <tfoot>
                <tr class="subtotal">
                    <td class="right" colspan="2">
                        SUBTOTAL {{ strtoupper($grup['cabang']) }} &middot;
                        {{ $grup['notas']->count() }} NOTA &middot;
                        {{ number_format($grup['notas']->sum(fn ($n) => $n['items']->sum('qty'))) }} ITEM
                    </td>
                    <td class="right">{{ number_format($grup['notas']->sum(fn ($n) => $n['items']->sum('qty'))) }}</td>
                    <td class="right">&nbsp;</td>
                    <td class="right" style="width: 14%;">
                        {{ \App\Support\Rupiah::format($grup['notas']->sum('total')) }}
                    </td>
                </tr>
            </tfoot>
        </table>
        @endforeach
    @empty
        <table class="data">
            <tbody>
                <tr>
                    <td class="center muted">Belum ada data untuk periode ini.</td>
                </tr>
            </tbody>
        </table>
    @endforelse

    {{-- Total gabungan seluruh cabang. Selalu dicetak di akhir supaya
         angka total keseluruhan tetap ada walau nota dipisah per cabang. --}}
    @if ($notas->isNotEmpty())
        <div class="grand">
            <span class="label">Rekap Nilai (Semua Cabang)</span>
            <div class="rinci">
                {{ $bagian->count() }} cabang &middot;
                {{ $notas->count() }} nota &middot;
                {{ number_format($notas->sum(fn ($n) => $n['items']->sum('qty'))) }} item
                @if ($namaBulan)
                    &middot; Periode {{ $namaBulan }} {{ $tahun }}
                @else
                    &middot; Periode Semua Bulan {{ $tahun }}
                @endif
            </div>

            @php
                // Semua item dikumpulkan lagi supaya baris Total / Diskon /
                // Grand Total di bawah ini pas dengan angka faktur.
                $semuaItem = $notas->flatMap(fn ($n) => $n['items']);
                $totalKotor = $semuaItem->sum(fn ($item) => (float) $item->total_kotor);
                $totalDiskon = $semuaItem->sum(fn ($item) => (float) $item->nominal_diskon);
            @endphp

            <table class="hitung">
                <tr>
                    <td>Total (sebelum diskon)</td>
                    <td class="angka strong">{{ \App\Support\Rupiah::format($totalKotor) }}</td>
                </tr>
                @if ($totalDiskon > 0)
                    <tr class="rumus">
                        <td>Diskon ({{ number_format($totalDiskon / max($totalKotor, 1) * 100, 2, ',', '.') }}%)</td>
                        <td class="angka strong">&minus; {{ \App\Support\Rupiah::format($totalDiskon) }}</td>
                    </tr>
                @endif
                <tr class="grand-total">
                    <td>Grand Total</td>
                    <td class="angka">{{ \App\Support\Rupiah::format($totalBelanja) }}</td>
                </tr>
            </table>
        </div>
    @endif

    <table class="sign">
        <tr>
            <td>
                Dibuat oleh,
                <div class="sign-space"></div>
                <span class="sign-name">Fiqri</span>
            </td>
            <td>
                Diketahui oleh,
                <div class="sign-space"></div>
                <span class="sign-name">(_________________________)</span>
            </td>
        </tr>
    </table>

    @php
        // Nomor halaman ditulis di sini, bukan di dalam <div class="footer">,
        // karena DomPDF baru tahu jumlah halaman setelah dokumen selesai
        // dirender. Teks footer-nya sendiri tetap ada di bawah sebagai teks
        // biasa, jadi tidak ada placeholder yang tercetak mentah.
        //
        // Font diambil lewat $fontMetrics, bukan null: kalau null, DomPDF
        // justru ikut menulis ulang placeholder "HAL. {PAGE_NUM} ..." apa
        // adanya dan halaman terakhir jadi kosong.
        $scriptHalaman = <<<'HTML'
<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
        $pdf->page_text(709, 572, 'HAL. {PAGE_NUM} / {PAGE_COUNT}', $font, 7, [0.58, 0.64, 0.72]);
    }
</script>
HTML;
    @endphp

    <div class="footer">Dokumen dibuat otomatis oleh sistem Riwayat Order Barang &mdash; PT. Warna Tanjung Jaya</div>

    {!! $scriptHalaman !!}
</body>
</html>