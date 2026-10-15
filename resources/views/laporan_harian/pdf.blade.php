<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian Oplosan</title>
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

        /* Kotak logo WTJ. Latarnya putih supaya logo tetap terbaca di atas
           banner biru; gambarnya di-scale ke dalam kotak.
           display: block wajib: sebagai span (inline), width/height tidak
           dipakai DomPDF dan kotaknya jadi pil tinggi yang menyembul
           keluar banner. */
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
            width: 25%;
            border-top: 3px solid #cbd5e1;
        }

        .summary .card-blue { background: #eff6ff; border: 1px solid #bfdbfe; border-top: 3px solid #2563eb; }
        .summary .card-amber { background: #fffbeb; border: 1px solid #fde68a; border-top: 3px solid #d97706; }
        .summary .card-violet { background: #f5f3ff; border: 1px solid #ddd6fe; border-top: 3px solid #7c3aed; }
        .summary .card-green { background: #ecfdf5; border: 1px solid #a7f3d0; border-top: 3px solid #059669; }

        /* Titik kecil di atas label, warnanya mengikuti kartunya. */
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
        .icon-violet { background: #7c3aed; }
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

        /* Tabel rincian pekerjaan tiap tanggal.
           Disamakan dengan tabel subtotal (table.data) supaya garis kolom
           atas-bawah tetap nyambung walau dua tabel terpisah. */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 0;
        }

        table.data th,
        table.data td,
        table.items th,
        table.items td {
            border: 1px solid #d7dee8;
            padding: 5px 6px;
            vertical-align: middle;
        }

        table.data th,
        table.items th {
            background: #1d4ed8;
            color: #fff;
            text-transform: uppercase;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: .25px;
        }

        table.data td { font-size: 8.4px; }
        table.items td { font-size: 8.4px; }

        /* Di dalam blok hari, garis atas header tidak perlu dobel dengan
           garis bawah kepala blok. */
        table.items thead th { border-top: none; }

        table.items tbody tr:nth-child(even) td { background: #f5f8ff; }

        /* Kolom keterangan: boleh turun baris, tapi kalau kosong tetap tampil
           rapi dengan tanda — supaya tidak ada kolom yang menganga kosong. */
        .col-ket { word-wrap: break-word; }

        table.data tbody tr:nth-child(even) td { background: #f5f8ff; }

        .col-no   { width: 4%;  }
        .col-qty  { width: 8%;  }
        .col-uang { width: 13%; }

        .right { text-align: right; }
        .center { text-align: center; }
        .strong { font-weight: 700; color: #0f172a; }
        .muted { color: #64748b; }

        .badge-match {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 8px;
            font-size: 7px;
            font-weight: 700;
        }
        .match-sama  { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .match-mirip { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .match-beda  { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }

        tfoot .total-row td {
            background: #fffbeb !important;
            border-top: 2px solid #f59e0b;
            font-weight: 700;
            color: #92400e;
            font-size: 8.5px;
        }

        /* ===== Blok per hari =====
           Satu tanggal = satu blok berisi seluruh pekerjaan hari itu.
           Bentuknya mengikuti blok nota di laporan oplosan. */
        .hari {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .hari-head {
            background: #eff6ff;
            border-bottom: 1px solid #bfdbfe;
            padding: 6px 9px;
        }

        .hari-head .tanggal {
            font-size: 9.5px;
            font-weight: 700;
            color: #1e3a8a;
        }

        .hari-head .info {
            font-size: 7.5px;
            color: #475569;
            margin-top: 1px;
        }

        .hari-head .total {
            float: right;
            font-size: 9.5px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Subtotal tiap hari, dicetak tepat di bawah pekerjaan hari itu. */
        .subtotal td {
            background: #eef2ff !important;
            border-top: 2px solid #1d4ed8;
            border-bottom: 1px solid #d7dee8;
            font-weight: 700;
            color: #1e293b;
            font-size: 8.5px;
            padding: 6px;
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
        .sign, .grand { page-break-inside: avoid; }

        /* Kotak total keseluruhan (gabungan semua tanggal). */
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
                    <p class="title">Laporan Harian Oplosan</p>
                    <p class="subtitle">Unit Body &amp; Paint &mdash; Tinter: Fiqri</p>
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
                <span class="badge">No. Dokumen: LHO-{{ $kodeDokumen }}-{{ $tahun }}{{ $namaBulan ? '-' . strtoupper(substr($namaBulan, 0, 3)) : '' }}</span>
                <span class="badge kosong">{{ $cabang ?: 'Seluruh Cabang' }}</span>
            </td>
            <td class="right">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td class="card-blue">
                <span class="icon-badge icon-blue">&#8801;</span>
                <span class="label">Total Pekerjaan</span>
                <span class="value">{{ number_format($totalBaris) }} baris</span>
            </td>
            <td class="card-amber">
                <span class="icon-badge icon-amber">&#931;</span>
                <span class="label">Total Volume</span>
                <span class="value">{{ number_format($totalVolume) }} CC</span>
            </td>
            <td class="card-violet">
                <span class="icon-badge icon-violet">&#9201;</span>
                <span class="label">Total Durasi</span>
                <span class="value">{{ $totalDurasi > 0 ? number_format($totalDurasi) . ' mnt' : '—' }}</span>
            </td>
            <td class="card-green">
                <span class="icon-badge icon-green">&#10003;</span>
                <span class="label">Matching Sama</span>
                <span class="value">
                    {{ number_format($jumlahSama) }} / {{ number_format($totalBaris) }}
                    ({{ $totalBaris > 0 ? round($jumlahSama / $totalBaris * 100) : 0 }}%)
                </span>
            </td>
        </tr>
    </table>

    @forelse ($hari as $blok)
        @php
            $namaHari = \App\Support\Tanggal::panjang($blok['tanggal']);
            $cabangHari = $blok['items']->pluck('cabang_area')->filter()->unique()->values();
            $platHari = $blok['items']->pluck('plat_nomor')->filter()->unique()->values();
        @endphp
        <div class="hari">
            <div class="hari-head">
                <span class="total">{{ number_format($blok['volume']) }} cc</span>
                <span class="tanggal">{{ $namaHari }}</span>
                <div class="info">
                    {{ $blok['jumlahItem'] }} pekerjaan
                    @if ($blok['durasi'] > 0)
                        &middot; {{ number_format($blok['durasi']) }} mnt
                    @endif
                    &middot; {{ $blok['sama'] }}/{{ $blok['jumlahItem'] }} matching sama
                    @if ($platHari->isNotEmpty())
                        &middot; {{ $platHari->take(3)->implode(' · ') }}{{ $platHari->count() > 3 ? ' +' . ($platHari->count() - 3) . ' lainnya' : '' }}
                    @endif
                    @if ($cabangHari->isNotEmpty())
                        &middot; {{ $cabangHari->implode(', ') }}
                    @endif
                </div>
            </div>

            <table class="items">
                <thead>
                    <tr>
                        <th class="center" style="width: 4.5%;">No</th>
                        <th style="width: 11%;">Plat Nomor</th>
                        <th style="width: 12%;">Kode Warna</th>
                        <th style="width: 12.5%;">Tipe Mobil</th>
                        <th style="width: 10.5%;">Bahan Cat</th>
                        <th class="right" style="width: 8.5%;">Volume</th>
                        <th class="center" style="width: 7%;">Dibuat</th>
                        <th class="center" style="width: 7%;">Selesai</th>
                        <th class="right" style="width: 7%;">Durasi</th>
                        <th class="center" style="width: 8.5%;">Matching</th>
                        <th style="width: 11.5%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blok['items'] as $item)
                        @php
                            $kelasMatch = ['Sama' => 'match-sama', 'Mirip' => 'match-mirip', 'Beda' => 'match-beda'][$item->hasil_matching] ?? '';
                        @endphp
                        <tr>
                            <td class="center muted">{{ $loop->iteration }}</td>
                            <td class="strong">{{ filled($item->plat_nomor) ? $item->plat_nomor : '—' }}</td>
                            <td>{{ filled($item->kode_warna) ? $item->kode_warna : '—' }}</td>
                            <td>{{ filled($item->tipe_mobil) ? $item->tipe_mobil : '—' }}</td>
                            <td>{{ filled($item->bahan_cat) ? $item->bahan_cat : '—' }}</td>
                            <td class="right strong">{{ number_format($item->volume_cc) }} cc</td>
                            <td class="center">{{ filled($item->jam_dibuat) ? \Illuminate\Support\Str::of($item->jam_dibuat)->substr(0, 5) : '—' }}</td>
                            <td class="center">{{ filled($item->jam_selesai) ? \Illuminate\Support\Str::of($item->jam_selesai)->substr(0, 5) : '—' }}</td>
                            <td class="right muted">{{ filled($item->durasi_menit) ? $item->durasi_menit . ' mnt' : '—' }}</td>
                            <td class="center"><span class="badge-match {{ $kelasMatch }}">{{ filled($item->hasil_matching) ? $item->hasil_matching : '—' }}</span></td>
                            <td class="col-ket">{{ filled($item->keterangan) ? $item->keterangan : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Subtotal tanggal ini, langsung di bawah pekerjaan harinya. --}}
        <table class="data">
            <tfoot>
                <tr class="subtotal">
                    <td class="right" colspan="5">
                        SUBTOTAL {{ strtoupper($namaHari) }} &middot;
                        {{ $blok['jumlahItem'] }} PEKERJAAN &middot;
                        {{ $blok['sama'] }} MATCHING SAMA
                    </td>
                    <td class="right">{{ number_format($blok['volume']) }} cc</td>
                    <td colspan="2" class="center">&nbsp;</td>
                    <td class="right">{{ $blok['durasi'] > 0 ? number_format($blok['durasi']) . ' mnt' : '—' }}</td>
                    <td class="center">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    @empty
        <table class="data">
            <tbody>
                <tr>
                    <td class="center muted">Belum ada data untuk periode ini.</td>
                </tr>
            </tbody>
        </table>
    @endforelse

    {{-- Total gabungan seluruh tanggal. --}}
    @if ($baris->isNotEmpty())
        <div class="grand">
            <span class="label">Total Keseluruhan</span>
            <span class="angka">{{ number_format($totalVolume) }} CC</span>
            <div class="rinci">
                {{ $hari->count() }} hari &middot;
                {{ number_format($totalBaris) }} pekerjaan &middot;
                {{ $totalDurasi > 0 ? number_format($totalDurasi) . ' mnt' : 'durasi belum diisi' }} &middot;
                {{ number_format($jumlahSama) }} matching sama
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

    <div class="footer">Dokumen dibuat otomatis oleh sistem Laporan Harian Oplosan &mdash; PT. Warna Tanjung Jaya</div>

    {!! $scriptHalaman !!}
</body>
</html>