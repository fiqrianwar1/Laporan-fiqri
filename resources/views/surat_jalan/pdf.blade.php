<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan</title>
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
            padding: 14px 26px 13px;
            margin: 0 -26px 0;
        }

        .banner table { width: 100%; border-collapse: collapse; }
        .banner td { vertical-align: middle; padding: 0; }

        /* Kotak logo WTJ. display: block wajib supaya width/height dipakai
           DomPDF - sebagai span (inline) kotaknya jadi pil tinggi. */
        .logo {
            display: block;
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: #ffffff;
            text-align: center;
            padding: 3px;
        }
        .logo img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            border-radius: 7px;
        }

        .logo .inisial {
            display: block;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 700;
            line-height: 40px;
            letter-spacing: .5px;
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

        .meta { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .meta td { padding: 0; font-size: 8.5px; color: #475569; }

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

        table.data td { font-size: 8.4px; }

        table.data tbody tr:nth-child(even) td { background: #f5f8ff; }

        .right { text-align: right; }
        .center { text-align: center; }
        .strong { font-weight: 700; color: #0f172a; }
        .muted { color: #64748b; }

        /* ===== Blok per surat =====
           Satu surat = satu blok berisi seluruh barangnya. Bentuknya
           mengikuti blok nota di laporan oplosan. */
        .surat {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .surat-head {
            background: #eff6ff;
            border-bottom: 1px solid #bfdbfe;
            padding: 6px 9px;
        }

        .surat-head .nomor {
            font-size: 9.5px;
            font-weight: 700;
            color: #1e3a8a;
        }

        .surat-head .info {
            font-size: 7.5px;
            color: #475569;
            margin-top: 1px;
        }

        .surat-head .total {
            float: right;
            font-size: 9.5px;
            font-weight: 700;
            color: #0f172a;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
        }

        table.items th {
            background: #f8fafc;
            color: #475569;
            text-transform: uppercase;
            font-size: 6.5px;
            font-weight: 700;
            letter-spacing: .25px;
            padding: 4px 6px;
            border-bottom: 1px solid #e2e8f0;
        }

        table.items td {
            font-size: 8.4px;
            padding: 5.5px 7px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: middle;
        }

        table.items tbody tr:nth-child(even) td { background: #fafcff; }

        table.items tr:last-child td { border-bottom: 0; }

        /* Subtotal tiap surat, dicetak tepat di bawah barangnya. */
        .subtotal td {
            background: #eef2ff !important;
            border-top: 2px solid #1d4ed8;
            border-bottom: 1px solid #d7dee8;
            font-weight: 700;
            color: #1e293b;
            font-size: 8.5px;
            padding: 6px;
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

        /* Kotak total keseluruhan (gabungan semua surat). */
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

        /* Baris tanda tangan & total tidak boleh kepotong ke halaman berikutnya. */
        .sign, .grand { page-break-inside: avoid; }

        /* ===== Pemisah cabang =====
           Surat tiap cabang dikelompokkan di bawah judulnya sendiri supaya
           Banjarmasin & Palangka tidak tercampur dalam satu deretan. */
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
    </style>
</head>
<body>
    <div class="banner">
        <table>
            <tr>
                @php $logo = \App\Support\Logo::path(); @endphp
                <td style="width: 54px;">
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
                    <p class="title">Surat Jalan</p>
                    <p class="subtitle">Barang Keluar Gudang &mdash; Unit Body &amp; Paint</p>
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
                    $kodeDokumen = $cabang ? \App\Support\Cabang::singkatan($cabang) : 'ALL';
                @endphp
                <span class="badge">No. Dokumen: SJ-{{ $kodeDokumen }}-{{ $tahun }}{{ $namaBulan ? '-' . strtoupper(substr($namaBulan, 0, 3)) : '' }}</span>
                <span class="badge kosong">{{ $cabang ?: 'Seluruh Cabang' }}</span>
            </td>
            <td class="right">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td class="card-blue">
                <span class="icon-badge icon-blue">&#8801;</span>
                <span class="label">Total Surat</span>
                <span class="value">{{ number_format($surat->count()) }} surat</span>
            </td>
            <td class="card-amber">
                <span class="icon-badge icon-amber">&#931;</span>
                <span class="label">Total Barang</span>
                <span class="value">{{ number_format($totalBaris) }} barang</span>
            </td>
            <td class="card-green">
                <span class="icon-badge icon-green">&#10003;</span>
                <span class="label">Total Jumlah Dikirim</span>
                <span class="value">{{ number_format($totalJumlah) }}</span>
            </td>
        </tr>
    </table>

    @php
        // Surat dipisah per cabang supaya Banjarmasin & Palangka tidak
        // tercampur, tapi totalnya tetap dijumlah bersama di akhir.
        $bagian = \App\Support\Cabang::kelompokkanNota($surat);
    @endphp

    @forelse ($bagian as $grup)
        <div class="bagian">
            <span class="nilai">{{ number_format($grup['notas']->sum(fn ($s) => $s['items']->sum('jumlah'))) }}</span>
            <span class="nama">Bagian {{ $loop->iteration }} &mdash; {{ $grup['cabang'] }}</span>
            <div class="ket">
                {{ $grup['notas']->count() }} surat &middot;
                {{ number_format($grup['notas']->sum(fn ($s) => $s['jumlahItem'])) }} barang &middot;
                {{ number_format($grup['notas']->sum(fn ($s) => $s['items']->sum('jumlah'))) }} total jumlah
                &middot; Kode: {{ $grup['kode'] }}
            </div>
        </div>

        @foreach ($grup['notas'] as $satu)
            @php
                $totalJumlahSurat = $satu['items']->sum('jumlah');
            @endphp
            <div class="surat">
                <div class="surat-head">
                    <span class="total">{{ number_format($totalJumlahSurat) }}</span>
                    <span class="nomor">
                        <span class="nomor-bagian">Surat #{{ $satu['nomorBagian'] }}</span>
                        {{ \App\Support\Tanggal::panjang($satu['tanggal']) }}
                    </span>
                    <div class="info">
                        {{ $satu['nomor'] ?? 'Tanpa nomor' }} &middot;
                        {{ $satu['items']->count() }} barang &middot;
                        {{ number_format($totalJumlahSurat) }} total jumlah
                        @if ($satu['items']->first()->cabang_area)
                            &middot; {{ $satu['items']->first()->cabang_area }}
                        @endif
                    </div>
                </div>

                <table class="items">
                    <thead>
                        <tr>
                            <th class="center col-no">No</th>
                            <th style="width: 13%;">Kode Barang</th>
                            <th style="width: 28%;">Nama Barang / Cat</th>
                            <th style="width: 17%;">Kemasan Barang</th>
                            <th class="right col-qty">Jumlah</th>
                            <th style="width: 17%;">Asal Penyimpanan</th>
                            <th style="width: 14%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($satu['items'] as $item)
                            <tr>
                                <td class="center item-no">{{ $item->nomor_urut ?? $loop->iteration }}</td>
                                <td class="muted">{{ $item->kode_barang ?: '—' }}</td>
                                <td class="strong">{{ $item->nama_barang }}</td>
                                <td>{{ $item->kemasan_barang ?: '—' }}</td>
                                <td class="right strong">{{ number_format($item->jumlah) }}</td>
                                <td>{{ $item->asal_penyimpanan ?: '—' }}</td>
                                <td class="muted">{{ $item->keterangan ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Subtotal cabang ini, langsung di bawah surat terakhirnya. --}}
            @if ($loop->last)
                <table class="data">
                    <tfoot>
                        <tr class="subtotal">
                            <td class="right" colspan="4">
                                SUBTOTAL {{ strtoupper($grup['cabang']) }} &middot;
                                {{ $grup['notas']->count() }} SURAT &middot;
                                {{ number_format($grup['notas']->sum(fn ($s) => $s['jumlahItem'])) }} BARANG
                            </td>
                            <td class="right">{{ number_format($grup['notas']->sum(fn ($s) => $s['items']->sum('jumlah'))) }}</td>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                    </tfoot>
                </table>
            @endif
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

    {{-- Total gabungan seluruh cabang. --}}
    @if ($surat->isNotEmpty())
        <div class="grand">
            <span class="label">Total Keseluruhan (Semua Cabang)</span>
            <span class="angka">{{ number_format($totalJumlah) }}</span>
            <div class="rinci">
                {{ $bagian->count() }} cabang &middot;
                {{ number_format($surat->count()) }} surat &middot;
                {{ number_format($totalBaris) }} barang
                @if ($namaBulan)
                    &middot; Periode {{ $namaBulan }} {{ $tahun }}
                @else
                    &middot; Periode Semua Bulan {{ $tahun }}
                @endif
            </div>
        </div>
    @endif

    <table class="sign">
        <tr>
            <td>
                Diserahkan oleh,
                <div class="sign-space"></div>
                <span class="sign-name">(_________________________)</span>
            </td>
            <td>
                Diterima oleh,
                <div class="sign-space"></div>
                <span class="sign-name">(_________________________)</span>
            </td>
        </tr>
    </table>

    @php
        // Nomor halaman ditulis di sini, bukan di dalam <div class="footer">,
        // karena DomPDF baru tahu jumlah halaman setelah dokumen selesai
        // dirender. Font diambil lewat $fontMetrics, bukan null: kalau null,
        // DomPDF justru ikut menulis ulang placeholder "HAL. {PAGE_NUM} ..."
        // apa adanya dan halaman terakhir jadi kosong.
        $scriptHalaman = <<<'HTML'
<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
        $pdf->page_text(709, 572, 'HAL. {PAGE_NUM} / {PAGE_COUNT}', $font, 7, [0.58, 0.64, 0.72]);
    }
</script>
HTML;
    @endphp

    <div class="footer">Dokumen dibuat otomatis oleh sistem Surat Jalan &mdash; PT. Warna Tanjung Jaya</div>

    {!! $scriptHalaman !!}
</body>
</html>
