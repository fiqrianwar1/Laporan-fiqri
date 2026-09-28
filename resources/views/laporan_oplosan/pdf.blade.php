<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Oplosan</title>
    <style>
        @page { margin: 0 26px 28px; }

        /* Ruang putih di atas banner supaya kop tidak nempel ke tepi kertas. */
        .top-gap { height: 22px; }

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

        .logo {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #ffffff;
            color: #1d4ed8;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            line-height: 44px;
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
        }

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
            padding: 6px 6px;
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

        /* Blok per nota: kepala nota + tabel bahan di bawahnya. */
        .nota {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .nota-head {
            background: #eff6ff;
            border-bottom: 1px solid #bfdbfe;
            padding: 6px 9px;
        }

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
            font-size: 8px;
            padding: 5px 6px;
            border-bottom: 1px solid #eef2f7;
            vertical-align: middle;
        }

        table.items tr:last-child td { border-bottom: 0; }

        /* Bukti foto nota. Semua thumbnail dipaksa 54x54px dengan object-fit
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

        .footer {
            position: fixed;
            bottom: -18px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5px;
            color: #94a3b8;
        }

        /* ===== Pemisah cabang =====
           Nota tiap cabang dikelompokkan di bawah judul "Bagian" sendiri,
           jadi Banjarmasin & Palangka tidak tercampur dalam satu deretan. */
        .bagian {
            margin: 14px 0 9px;
            border-left: 4px solid #1d4ed8;
            background: #f1f5f9;
            padding: 7px 12px;
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
        .subtotal td {
            background: #f1f5f9 !important;
            font-weight: 700;
            color: #1e293b;
            font-size: 8px;
            border-top: 2px solid #94a3b8;
        }

        /* Kotak total keseluruhan (gabungan semua cabang). */
        .grand {
            margin-top: 14px;
            border: 2px solid #1d4ed8;
            border-radius: 8px;
            background: #eff6ff;
            padding: 10px 14px;
        }

        .grand .label {
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
    <div class="top-gap"></div>
    <div class="banner">
        <table>
            <tr>
                <td style="width: 52px;"><span class="logo">WTJ</span></td>
                <td>
                    <p class="brand">PT. Warna Tanjung Jaya</p>
                    <p class="title">Laporan Oplosan</p>
                    <p class="subtitle">Unit Body &amp; Paint Wira Toyota Banjarmasin &mdash; Tinter: Fiqri</p>
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
                <span class="badge">No. Dokumen: LO-{{ $kodeDokumen }}-{{ $tahun }}{{ $namaBulan ? '-' . strtoupper(substr($namaBulan, 0, 3)) : '' }}</span>
                <span class="badge" style="background:#f1f5f9;color:#475569;">
                    {{ $cabang ?: 'Seluruh Cabang' }}
                </span>
            </td>
            <td class="right">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td class="card-blue">
                <span class="icon-badge icon-blue">&#8801;</span>
                <span class="label">Total Nota / Item</span>
                <span class="value">{{ $notas->count() }} nota &middot; {{ number_format($totalOplosan) }} item</span>
            </td>
            <td class="card-amber">
                <span class="icon-badge icon-amber">&#931;</span>
                <span class="label">Total Qty (CC/LTR)</span>
                <span class="value">{{ number_format($totalCc) }}</span>
            </td>
            <td class="card-green">
                <span class="icon-badge icon-green">Rp</span>
                <span class="label">Total Biaya</span>
                <span class="value">{{ \App\Support\Rupiah::format($totalBiaya) }}</span>
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
                {{ number_format($grup['notas']->sum(fn ($n) => $n['items']->count())) }} item &middot;
                {{ number_format($grup['notas']->sum(fn ($n) => $n['items']->sum('qty_cc'))) }} CC/LTR
                &middot; Kode: {{ $grup['kode'] }}
            </div>
        </div>

        @foreach ($grup['notas'] as $nota)
        @php
            $platUtama = $nota['items']->first()->no_plat;
            $unitSama = $nota['items']->every(fn ($item) => $item->no_plat === $platUtama);

            // Kolom No. Plat selalu dicetak selama ada item yang punya plat.
            $adaPlat = $nota['items']->contains(fn ($item) => filled($item->no_plat));

            // Satu nota bisa memuat beberapa unit - semuanya ditulis di judul
            // blok supaya tidak ada unit yang hilang dari cetakan.
            $platNota = $nota['items']->pluck('no_plat')->filter()->unique()->values();
            $platRingkas = $platNota->take(2)->implode(' \u00b7 ')
                . ($platNota->count() > 2 ? ' +' . ($platNota->count() - 2) . ' lainnya' : '');

            // Foto bukti nota. Tiap item sering di-upload foto yang isinya sama,
            // jadi disaring pakai isi filenya dulu, baru disematkan sebagai base64
            // supaya DomPDF tidak perlu mengambil file dari URL.
            $fotoNota = \App\Support\FotoNota::unik($nota['items'], 'foto_nota', 6)
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
        {{-- Satu nota = satu blok berisi seluruh item bahannya. --}}
        <div class="nota">
            <div class="nota-head">
                <span class="total">{{ \App\Support\Rupiah::format($nota['total']) }}</span>
                <span class="nomor">
                    <span class="nomor-bagian">Nota #{{ $nota['nomorBagian'] }}</span>
                    {{ \App\Support\Tanggal::panjang($nota['tanggal']) }}
                </span>
                <div class="info">
                    {{ $nota['nomor'] ?? 'Tanpa nomor' }} &middot;
                    @if ($platRingkas)
                        {{ $platRingkas }} &middot;
                    @endif
                    {{ $nota['items']->count() }} item &middot;
                    {{ number_format($nota['items']->sum('qty_cc')) }} CC/LTR
                    @if ($unitSama && $nota['items']->first()->kode_warna_unit)
                        &middot; {{ $nota['items']->first()->kode_warna_unit }}
                    @endif
                </div>
            </div>

            @if ($fotoNota->isNotEmpty())
                <div class="nota-foto">
                    <span class="label">Bukti Foto</span><img src="{{ $fotoNota->first() }}" alt="Foto nota">
                    @foreach ($fotoNota->slice(1) as $gambar)
                        <img src="{{ $gambar }}" alt="Foto nota">
                    @endforeach
                </div>
            @endif

            <table class="items">
                <thead>
                    <tr>
                        <th class="center" style="width: 5%;">No</th>
                        <th style="width: 16%;">Warna / Unit</th>
                        @if ($adaPlat)
                            <th style="width: 13%;">No. Plat</th>
                        @endif
                        <th>Rincian Bahan</th>
                        <th class="right" style="width: 8%;">Qty</th>
                        <th class="right" style="width: 13%;">Harga</th>
                        <th class="right" style="width: 10%;">Diskon</th>
                        <th class="right" style="width: 12%;">Total Item</th>
                        <th class="right" style="width: 11%;">Harga / CC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nota['items'] as $item)
                        <tr>
                            <td class="center item-no">{{ $item->nomor_urut ?? $loop->iteration }}</td>
                            <td class="strong">{{ $item->kode_warna_unit }}</td>
                            @if ($adaPlat)
                                <td>{{ $item->no_plat }}</td>
                            @endif
                            <td>{{ $item->rincian_bahan }}</td>
                            <td class="right">{{ number_format($item->qty_cc) }}</td>
                            <td class="right">{{ \App\Support\Rupiah::format($item->harga_nota) }}</td>
                            <td class="right muted">
                                @php $nominalDiskon = (float) $item->nominal_diskon; @endphp
                                {{ \App\Support\Rupiah::formatRingkas($nominalDiskon) }}
                            </td>
                            <td class="right strong">{{ \App\Support\Rupiah::format((float) $item->harga_nota - $nominalDiskon) }}</td>
                            <td class="right muted">
                                {{ $item->qty_cc > 0 ? \App\Support\Rupiah::format((float) $item->harga_nota / $item->qty_cc) : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Subtotal cabang ini, langsung di bawah notanya yang terakhir. --}}
        <table class="data">
            <tfoot>
                <tr class="subtotal">
                    <td class="right">
                        SUBTOTAL {{ strtoupper($grup['cabang']) }} &middot;
                        {{ $grup['notas']->count() }} NOTA &middot;
                        {{ number_format($grup['notas']->sum(fn ($n) => $n['items']->count())) }} ITEM &middot;
                        {{ number_format($grup['notas']->sum(fn ($n) => $n['items']->sum('qty_cc'))) }} CC/LTR
                    </td>
                    <td class="right" style="width: 14%;">
                        {{ \App\Support\Rupiah::format($grup['notas']->sum('total')) }}
                    </td>
                    <td class="right" style="width: 10%;">&nbsp;</td>
                    <td class="right" style="width: 11%;">&nbsp;</td>
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
            <span class="angka">{{ \App\Support\Rupiah::format($totalBiaya) }}</span>
            <span class="label">Total Keseluruhan (Semua Cabang)</span>
            <div class="rinci">
                {{ $bagian->count() }} cabang &middot;
                {{ $notas->count() }} nota &middot;
                {{ number_format($totalOplosan) }} item &middot;
                {{ number_format($totalCc) }} CC/LTR
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
                Dibuat oleh,
                <div class="sign-space"></div>
                <span class="sign-name">Fiqri (Tinter)</span>
            </td>
            <td>
                Diketahui oleh,
                <div class="sign-space"></div>
                <span class="sign-name">(_________________________)</span>
            </td>
        </tr>
    </table>

    <div class="footer">Dokumen dibuat otomatis oleh sistem Laporan Oplosan &mdash; PT. Warna Tanjung Jaya</div>
</body>
</html>
