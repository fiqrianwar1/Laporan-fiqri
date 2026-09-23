<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Oplosan</title>
    <style>
        @page { margin: 0 26px 28px; }

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
            color: #fff;
            padding: 18px 26px 16px;
            margin: 0 -26px 16px;
        }

        .banner .brand {
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #bfdbfe;
            margin: 0 0 4px;
            font-weight: 700;
        }

        .banner .title {
            font-size: 19px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .banner .subtitle {
            font-size: 9px;
            color: #dbeafe;
            margin: 0;
        }

        .banner-accent {
            height: 5px;
            background: #f59e0b;
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
            padding: 12px 14px;
            width: 33%;
        }

        .summary .card-blue { background: #eff6ff; border: 1px solid #bfdbfe; }
        .summary .card-amber { background: #fffbeb; border: 1px solid #fde68a; }
        .summary .card-green { background: #ecfdf5; border: 1px solid #a7f3d0; }

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

        .item-no {
            color: #94a3b8;
            font-weight: 700;
        }

        .sign {
            margin-top: 32px;
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
            padding: 10px 8px 12px;
        }

        .sign-space { height: 36px; }

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
    </style>
</head>
<body>
    <div class="banner">
        <p class="brand">PT. Warna Tanjung Jaya</p>
        <p class="title">Laporan Oplosan</p>
        <p class="subtitle">Unit Body &amp; Paint Wira Toyota Banjarmasin &mdash; Tinter: Fiqri</p>
    </div>
    <div class="banner-accent"></div>

    <table class="meta">
        <tr>
            <td>
                <span class="badge">
                    @if ($namaBulan)
                        {{ $namaBulan }} {{ $tahun }}
                    @else
                        Semua Bulan {{ $tahun }}
                    @endif
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
                <span class="value">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    @forelse ($notas as $nota)
        @php
            $platUtama = $nota['items']->first()->no_plat;
            $unitSama = $nota['items']->every(fn ($item) => $item->no_plat === $platUtama);
        @endphp
        {{-- Satu nota = satu blok berisi seluruh item bahannya. --}}
        <div class="nota">
            <div class="nota-head">
                <span class="total">Rp {{ number_format($nota['total'], 0, ',', '.') }}</span>
                <span class="nomor">{{ $platUtama ?? 'Tanpa plat' }}</span>
                <div class="info">
                    {{ $nota['nomor'] ?? 'Tanpa nomor' }} &middot;
                    {{ $nota['tanggal']?->format('d/m/Y') }} &middot;
                    {{ $nota['items']->count() }} item &middot;
                    {{ number_format($nota['items']->sum('qty_cc')) }} CC/LTR
                    @if ($unitSama && $nota['items']->first()->kode_warna_unit)
                        &middot; {{ $nota['items']->first()->kode_warna_unit }}
                    @endif
                </div>
            </div>

            <table class="items">
                <thead>
                    <tr>
                        <th class="center" style="width: 5%;">No</th>
                        <th style="width: 20%;">Warna / Unit</th>
                        @if (! $unitSama)
                            <th style="width: 12%;">No. Plat</th>
                        @endif
                        <th>Rincian Bahan</th>
                        <th class="right" style="width: 8%;">Qty</th>
                        <th class="right" style="width: 14%;">Harga</th>
                        <th class="right" style="width: 11%;">Harga / CC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nota['items'] as $item)
                        <tr>
                            <td class="center item-no">{{ $item->nomor_urut ?? $loop->iteration }}</td>
                            <td class="strong">{{ $item->kode_warna_unit }}</td>
                            @if (! $unitSama)
                                <td>{{ $item->no_plat }}</td>
                            @endif
                            <td>{{ $item->rincian_bahan }}</td>
                            <td class="right">{{ number_format($item->qty_cc) }}</td>
                            <td class="right">Rp {{ number_format($item->harga_nota, 0, ',', '.') }}</td>
                            <td class="right muted">
                                {{ $item->qty_cc > 0 ? 'Rp ' . number_format($item->harga_nota / $item->qty_cc, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <table class="data">
            <tbody>
                <tr>
                    <td class="center muted">Belum ada data untuk periode ini.</td>
                </tr>
            </tbody>
        </table>
    @endforelse
    @if ($notas->isNotEmpty())
        <table class="data">
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="right">
                        TOTAL {{ $notas->count() }} NOTA &middot; {{ number_format($totalOplosan) }} ITEM &middot; {{ number_format($totalCc) }} CC/LTR
                    </td>
                    <td class="right" style="width: 14%;">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                    <td class="right" style="width: 11%;">&nbsp;</td>
                </tr>
            </tfoot>
        </table>
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
