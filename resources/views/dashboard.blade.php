@extends('layouts.app')

@section('title', 'Dashboard')

@php
    use App\Support\Cabang;
    use App\Support\Rupiah;
@endphp
@section('content')
    @php
        $namaBulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
        // Periode default dashboard adalah tahun berjalan, jadi kalau tidak
        // ada bulan dipilih labelnya jangan bilang "Semua periode" - itu
        // keliru dan bikin angka di layar terlihat tidak cocok.
        if ($bulan) {
            $labelPeriode = \Illuminate\Support\Carbon::create(null, (int) $bulan, 1)->translatedFormat('F') . ' / ' . $tahun;
        } elseif ($semuaPeriode ?? false) {
            $labelPeriode = 'Semua periode';
        } else {
            $labelPeriode = 'Tahun ' . $tahun;
        }
        $labelPeriode .= $cabang ? ' · ' . $cabang : '';

        // Skala grafik tren: ambil nilai tertinggi dari kedua seri.
        $maksTren = collect($tren)->max(fn ($b) => max($b['oplosan'], $b['order'])) ?: 1;
        $totalKeseluruhan = $totalOplosan + $totalOrder;
    @endphp

    <div class="space-y-5">
        {{-- ================= HEADER HALAMAN ================= --}}
        <div class="page-head">
            <div>
                <div class="pill pill-blue mb-2">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-600"></span>
                    Ringkasan Statistik
                </div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-sub">
                    Pantau rekap oplosan &amp; belanja bahan dalam satu tampilan.
                    <span class="font-semibold text-slate-600">Periode: {{ $labelPeriode }}</span>
                </p>
            </div>

            <div class="page-actions">
                <a href="{{ route('laporan-oplosan.index') }}" class="btn btn-outline btn-sm">
                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 3v2m6-2v2M4 8h16M6 6h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                    </svg>
                    Laporan Oplosan
                </a>
                <a href="{{ route('riwayat-order.index') }}" class="btn btn-outline btn-sm">
                    <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Riwayat Order
                </a>
                @if (auth()->user()->role === 'tinter')
                    <a href="{{ route('riwayat-order.create') }}" class="btn btn-primary btn-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Catat Pembelian
                    </a>
                @endif
            </div>
        </div>

        {{-- ================= KARTU STATISTIK UTAMA ================= --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $kartuUtama = [
                    [
                        'label' => 'Total Laporan Oplosan',
                        'nilai' => number_format($totalOplosan),
                        'sub'   => Rupiah::format($totalBiayaOplosan) . ' total biaya',
                        'warna' => 'from-blue-500 to-blue-600',
                        'glow'  => 'bg-blue-500/10 group-hover:bg-blue-500/20',
                        'icon'  => 'M9 17V7m6 10V7M4 4h16v16H4z',
                    ],
                    [
                        'label' => 'Total Qty Oplosan',
                        'nilai' => number_format($totalCc) . ' CC',
                        'sub'   => 'Rata-rata ' . Rupiah::format($rataOplosan) . ' / laporan',
                        'warna' => 'from-amber-400 to-orange-500',
                        'glow'  => 'bg-amber-500/10 group-hover:bg-amber-500/20',
                        'icon'  => 'M5 13l4 4L19 7',
                    ],
                    [
                        'label' => 'Total Item Dibeli',
                        'nilai' => number_format($totalOrder),
                        'sub'   => $topBarang->isNotEmpty() ? 'Terbanyak: ' . $topBarang->first()['nama'] : 'Belum ada data',
                        'warna' => 'from-indigo-500 to-violet-600',
                        'glow'  => 'bg-indigo-500/10 group-hover:bg-indigo-500/20',
                        'icon'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    ],
                    [
                        'label' => 'Total Belanja',
                        'nilai' => Rupiah::format($totalBelanja),
                        'sub'   => 'Rata-rata ' . Rupiah::format($rataOrder) . ' / item',
                        'warna' => 'from-emerald-400 to-emerald-600',
                        'glow'  => 'bg-emerald-500/10 group-hover:bg-emerald-500/20',
                        'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v-2',
                    ],
                ];
            @endphp

            @foreach ($kartuUtama as $kartu)
                {{-- Ukuran kartu diatur .stat-card (lihat app.css). --}}
        <div class="stat-card">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full {{ $kartu['glow'] }} blur-2xl transition-all"></div>
                    <div class="relative z-10 flex items-start gap-3.5">
                        <div class="stat-icon bg-gradient-to-br {{ $kartu['warna'] }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kartu['icon'] }}"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="stat-label">{{ $kartu['label'] }}</p>
                            <p class="angka-kartu mt-1">{{ $kartu['nilai'] }}</p>
                            <p class="mt-0.5 truncate text-xs text-slate-500">{{ $kartu['sub'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================= KARTU STATISTIK LAPORAN HARIAN & SURAT JALAN ================= --}}
        {{-- Dua kartu ini melengkapi kartu utama: laporan harian menunjukkan
             mutu kerja oplosan (persentase matching warna), surat jalan
             menunjukkan berapa barang yang keluar gudang. --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $kartuTambahan = [
                    [
                        'label' => 'Total Laporan Harian',
                        'nilai' => number_format($totalHarian),
                        'sub'   => number_format($totalVolumeHarian) . ' cc total volume',
                        'warna' => 'from-emerald-500 to-teal-600',
                        'glow'  => 'bg-emerald-500/10 group-hover:bg-emerald-500/20',
                        'icon'  => 'M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    ],
                    [
                        'label' => 'Matching Warna Sama',
                        'nilai' => $persenMatchingSama . '%',
                        'sub'   => number_format($jumlahMatchingSama) . ' dari ' . number_format($totalHarian) . ' pekerjaan'
                            . ($rataDurasiHarian > 0 ? ' · rata-rata ' . $rataDurasiHarian . ' menit' : ''),
                        'warna' => 'from-violet-500 to-fuchsia-600',
                        'glow'  => 'bg-violet-500/10 group-hover:bg-violet-500/20',
                        'icon'  => 'M5 13l4 4L19 7',
                    ],
                    [
                        'label' => 'Total Surat Jalan',
                        'nilai' => number_format($totalSurat),
                        'sub'   => number_format($totalBarisSurat) . ' baris barang keluar',
                        'warna' => 'from-rose-500 to-orange-500',
                        'glow'  => 'bg-rose-500/10 group-hover:bg-rose-500/20',
                        'icon'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    ],
                    [
                        'label' => 'Total Barang Keluar',
                        'nilai' => number_format($totalBarangKeluar),
                        'sub'   => 'Rata-rata ' . number_format($rataPerSurat, 1) . ' barang / surat',
                        'warna' => 'from-sky-500 to-blue-600',
                        'glow'  => 'bg-sky-500/10 group-hover:bg-sky-500/20',
                        'icon'  => 'M5 17a2 2 0 104 0 2 2 0 00-4 0Zm10 0a2 2 0 104 0 2 2 0 00-4 0ZM3 6h2l2.4 10.2A2 2 0 009.35 17.6h8.3a2 2 0 001.95-1.6L21 9H6',
                    ],
                ];
            @endphp

            @foreach ($kartuTambahan as $kartu)
                <div class="stat-card">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full {{ $kartu['glow'] }} blur-2xl transition-all"></div>
                    <div class="relative z-10 flex items-start gap-3.5">
                        <div class="stat-icon bg-gradient-to-br {{ $kartu['warna'] }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kartu['icon'] }}"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="stat-label">{{ $kartu['label'] }}</p>
                            <p class="angka-kartu mt-1">{{ $kartu['nilai'] }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $kartu['sub'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================= FILTER PERIODE ================= --}}
        @php
            $chipFilter = function (array $query) use ($bulan, $tahun, $cabang) {
                $query['cabang'] = $cabang;

                $sama = (string) ($query['bulan'] ?? '') === (string) ($bulan ?? '')
                    && (string) ($query['tahun'] ?? '') === (string) ($tahun ?? '');

                return ['aktif' => $sama, 'url' => route('dashboard', array_filter($query, fn ($v) => filled($v)))];
            };

            $chips = [
                ['label' => 'Bulan ini', ...$chipFilter(['bulan' => now()->month, 'tahun' => now()->year])],
                ['label' => 'Tahun ' . now()->year, ...$chipFilter(['tahun' => now()->year])],
                ['label' => 'Semua', ...$chipFilter([])],
            ];
        @endphp
        {{-- Panel filter: satu bentuk dengan halaman daftar lain -
             kepala panel berisi judul + jumlah data, isi panel berisi
             chip periode cepat dan form filter. --}}
        <div class="filter-panel">
            <div class="filter-panel-head">
                <div class="icon-badge bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-md shadow-slate-800/20">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L15 12.414V19l-6 3v-9.586L3.293 6.707A1 1 0 0 1 3 6V4Z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold leading-tight text-slate-900">Filter Periode</p>
                    <p class="text-[11px] leading-tight text-slate-500">
                        {{ number_format($totalKeseluruhan) }} data &middot; {{ $labelPeriode }}
                    </p>
                </div>
                <div class="flex shrink-0 flex-wrap items-center gap-2">
                    @foreach ($chips as $chip)
                        <a href="{{ $chip['url'] }}" class="chip {{ $chip['aktif'] ? 'active' : '' }}">{{ $chip['label'] }}</a>
                    @endforeach
                </div>
            </div>

            <div class="filter-panel-body">
                {{-- Form filter: satu baris di layar lebar. --}}
                <form method="GET" action="{{ route('dashboard') }}" class="filter-baris items-end">
                    <div>
                        <label for="bulan" class="label">Bulan</label>
                        <div class="relative">
                            <select id="bulan" name="bulan" class="input appearance-none pr-10">
                                <option value="">Semua Bulan</option>
                                @foreach ($namaBulan as $val => $label)
                                    <option value="{{ $val }}" {{ (string) $bulan === $val || (string) $bulan === (string) (int) $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="tahun" class="label">Tahun</label>
                        <div class="relative">
                            <select id="tahun" name="tahun" class="input appearance-none pr-10">
                                <option value="">Semua Tahun</option>
                                @foreach ($daftarTahun as $th)
                                    <option value="{{ $th }}" {{ (string) $tahun === (string) $th ? 'selected' : '' }}>{{ $th }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="cabang" class="label">Cabang / Area</label>
                        <div class="relative">
                            <select id="cabang" name="cabang" class="input appearance-none pr-10">
                                <option value="">Semua Cabang</option>
                                @foreach (Cabang::daftar() as $c)
                                    <option value="{{ $c }}" {{ $cabang === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="btn btn-dark h-[42px] flex-1 min-w-0">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"/>
                            </svg>
                            <span class="truncate">Terapkan</span>
                        </button>

                        @if ($bulan || $tahun || $cabang)
                            <a href="{{ route('dashboard') }}" class="btn btn-ghost h-[42px] shrink-0 px-3">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

                    {{-- ================= TREN + BULAN INI ================= --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
            {{-- Grafik tren 6 bulan (CSS bar, tanpa library) --}}
            <div class="card overflow-hidden xl:col-span-2">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-md shadow-blue-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V5m0 14h16M8 16V9m4 7v-4m4 4v-7"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Tren 6 Bulan</h2>
                            <p class="card-sub">Laporan oplosan, item order, jumlah hari laporan harian &amp; surat jalan per bulan.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-[11px] font-semibold text-slate-500">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span> Oplosan
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-indigo-400"></span> Order
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Harian
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span> Surat
                        </span>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="flex h-52 items-end justify-between gap-1.5 sm:gap-3">
                        @foreach ($tren as $baris)
                            @php
                                $tinggiOplosan = round(($baris['oplosan'] / $maksTren) * 100);
                                $tinggiOrder = round(($baris['order'] / $maksTren) * 100);
                                $tinggiHarian = round(($baris['harian'] / $maksTren) * 100);
                                $tinggiSurat = round(($baris['surat'] / $maksTren) * 100);
                            @endphp
                            <div class="group flex h-full flex-1 flex-col items-center justify-end gap-2">
                                <div class="flex h-full w-full items-end justify-center gap-0.5 sm:gap-1">
                                    <div class="relative flex h-full w-1/4 max-w-[18px] items-end">
                                        <div class="w-full rounded-t-lg bg-gradient-to-t from-blue-600 to-blue-400 transition-all duration-500 group-hover:from-blue-700"
                                             style="height: {{ max($tinggiOplosan, 2) }}%"
                                             title="{{ $baris['label'] }} · {{ $baris['oplosan'] }} laporan oplosan ({{ number_format($baris['cc']) }} CC)"></div>
                                    </div>
                                    <div class="relative flex h-full w-1/4 max-w-[18px] items-end">
                                        <div class="w-full rounded-t-lg bg-gradient-to-t from-indigo-500 to-indigo-300 transition-all duration-500"
                                             style="height: {{ max($tinggiOrder, 2) }}%"
                                             title="{{ $baris['label'] }} · {{ $baris['order'] }} item order ({{ Rupiah::format($baris['belanja']) }})"></div>
                                    </div>
                                    <div class="relative flex h-full w-1/4 max-w-[18px] items-end">
                                        <div class="w-full rounded-t-lg bg-gradient-to-t from-emerald-600 to-emerald-400 transition-all duration-500"
                                             style="height: {{ max($tinggiHarian, 2) }}%"
                                             title="{{ $baris['label'] }} · {{ $baris['harian'] }} hari laporan harian ({{ number_format($baris['volume']) }} cc)"></div>
                                    </div>
                                    <div class="relative flex h-full w-1/4 max-w-[18px] items-end">
                                        <div class="w-full rounded-t-lg bg-gradient-to-t from-rose-500 to-rose-300 transition-all duration-500"
                                             style="height: {{ max($tinggiSurat, 2) }}%"
                                             title="{{ $baris['label'] }} · {{ $baris['surat'] }} surat jalan keluar"></div>
                                    </div>
                                </div>
                                <p class="text-[11px] font-semibold text-slate-500">{{ $baris['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Statistik bulan berjalan --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Bulan Ini</h2>
                            <p class="card-sub">{{ now()->translatedFormat('F Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-2.5 p-4 sm:p-5">
                    @php
                        $ringkas = [
                            ['label' => 'Belanja bulan ini', 'nilai' => Rupiah::format($ringkasBulanIni['belanja']), 'warna' => 'text-emerald-600'],
                            ['label' => 'Item order masuk', 'nilai' => number_format($ringkasBulanIni['order']) . ' item', 'warna' => 'text-indigo-600'],
                            ['label' => 'Laporan oplosan', 'nilai' => number_format($ringkasBulanIni['oplosan']) . ' laporan', 'warna' => 'text-blue-600'],
                            ['label' => 'Total qty oplosan', 'nilai' => number_format($ringkasBulanIni['cc']) . ' CC', 'warna' => 'text-amber-600'],
                            ['label' => 'Laporan harian oplosan', 'nilai' => number_format($ringkasBulanIni['harian']) . ' pekerjaan', 'warna' => 'text-emerald-600'],
                            ['label' => 'Volume harian oplosan', 'nilai' => number_format($ringkasBulanIni['volume']) . ' cc', 'warna' => 'text-teal-600'],
                            ['label' => 'Surat jalan keluar', 'nilai' => number_format($ringkasBulanIni['surat']) . ' surat', 'warna' => 'text-rose-600'],
                        ];
                    @endphp

                    @foreach ($ringkas as $item)
                        <div class="flex items-center justify-between gap-3 rounded-xl border-slate-200/70 bg-white/70 px-3.5 py-3 transition-colors hover:border-slate-300 hover:bg-white">
                            <span class="text-sm font-medium text-slate-600">{{ $item['label'] }}</span>
                            <span class="text-sm font-bold {{ $item['warna'] }}">{{ $item['nilai'] }}</span>
                        </div>
                    @endforeach

                    <p class="pt-1 text-[11px] leading-relaxed text-slate-400">
                        Angka ini selalu dihitung dari awal bulan berjalan, tidak terpengaruh filter periode di atas.
                    </p>
                </div>
            </div>
        </div>

        {{-- ================= TOP BARANG & TOP WARNA ================= --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            {{-- Barang paling sering dibeli --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-md shadow-indigo-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Barang Paling Sering Dibeli</h2>
                            <p class="card-sub">Diurutkan dari total qty pada periode ini.</p>
                        </div>
                    </div>
                </div>

                @if ($topBarang->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada data barang</p>
                        <p class="mt-1 text-xs text-slate-500">Catat pembelian dulu untuk melihat peringkatnya.</p>
                    </div>
                @else
                    @php $maksBarang = $topBarang->max('qty') ?: 1; @endphp
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($topBarang as $index => $barang)
                            <li class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-blue-50/40 sm:px-5">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold {{ $index === 0 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $index + 1 }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $barang['nama'] }}</p>
                                    <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-violet-500"
                                             style="width: {{ max(round(($barang['qty'] / $maksBarang) * 100), 4) }}%"></div>
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-bold text-slate-900">{{ number_format($barang['qty']) }} <span class="text-[11px] font-semibold text-slate-400">{{ $barang['satuan'] }}</span></p>
                                    <p class="text-[11px] text-slate-500">{{ Rupiah::format($barang['belanja']) }}</p>                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Kode warna paling sering dioplos --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-md shadow-amber-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Kode Warna Terbanyak</h2>
                            <p class="card-sub">Diurutkan dari total qty CC pada periode ini.</p>
                        </div>
                    </div>
                </div>

                @if ($topWarna->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada data oplosan</p>
                        <p class="mt-1 text-xs text-slate-500">Catat laporan oplosan dulu untuk melihat peringkatnya.</p>
                    </div>
                @else
                    @php $maksWarna = $topWarna->max('cc') ?: 1; @endphp
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($topWarna as $index => $warna)
                            <li class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-blue-50/40 sm:px-5">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold {{ $index === 0 ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $index + 1 }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $warna['kode'] }}</p>
                                    <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-orange-500"
                                             style="width: {{ max(round(($warna['cc'] / $maksWarna) * 100), 4) }}%"></div>
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-bold text-slate-900">{{ number_format($warna['cc']) }} <span class="text-[11px] font-semibold text-slate-400">CC</span></p>
                                    <p class="text-[11px] text-slate-500">{{ number_format($warna['unit']) }} unit · {{ Rupiah::format($warna['biaya']) }}</p>                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- ================= RINGKASAN PER CABANG ================= --}}
        <div class="card overflow-hidden">
            <div class="card-head flex">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-md shadow-slate-800/25">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="card-title">Laporan per Cabang</h2>
                        <p class="card-sub">
                            Total nota &amp; nilainya per cabang pada periode ini.
                            Satu nota dihitung sekali walau isinya banyak item.
                        </p>
                    </div>
                </div>
                <span class="pill pill-slate">{{ number_format($perCabang->count()) }} Cabang</span>
            </div>

            <div class="grid grid-cols-1 gap-4 p-4 lg:grid-cols-2 sm:p-5">
                @foreach ($perCabang as $c)
                    @php
                        $persen = $totalKeseluruhan > 0
                            ? round(($c['total_nota'] / max($totalKeseluruhan, 1)) * 100)
                            : 0;
                    @endphp
                    <div class="group relative overflow-hidden rounded-2xl border-slate-200/70 bg-gradient-to-br from-white to-slate-50/80 p-4 transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-lg sm:p-5">
                        <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-blue-500/5 blur-2xl transition group-hover:bg-blue-500/10"></div>

                        <div class="relative z-10">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-slate-800">{{ $c['cabang'] }}</p>
                                    <p class="mt-0.5 text-[11px] text-slate-500">
                                        {{ number_format($c['total_nota']) }} nota total
                                        &middot; {{ $persen }}% dari seluruh data
                                    </p>
                                </div>
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                                    </svg>
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                {{-- Kolom Oplosan --}}
                                <div class="rounded-xl border-blue-100 bg-blue-50/50 p-3">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-blue-700">Oplosan</p>                                    <p class="mt-1 text-lg font-bold tracking-tight text-slate-900">
                                        {{ number_format($c['nota_oplosan']) }} <span class="text-[11px] font-semibold text-slate-400">nota</span>
                                    </p>
                                    <p class="text-[11px] font-semibold text-blue-700">{{ Rupiah::format($c['nilai_oplosan']) }}</p>                                    <p class="mt-0.5 text-[10px] text-slate-500">{{ number_format($c['cc_oplosan']) }} CC</p>
                                </div>

                                {{-- Kolom Order --}}
                                <div class="rounded-xl border-indigo-100 bg-indigo-50/50 p-3">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-700">Order</p>
                                    <p class="mt-1 text-lg font-bold tracking-tight text-slate-900">
                                        {{ number_format($c['nota_order']) }} <span class="text-[11px] font-semibold text-slate-400">nota</span>
                                    </p>
                                    <p class="text-[11px] font-semibold text-indigo-700">{{ Rupiah::format($c['nilai_order']) }}</p>
                                    <p class="mt-0.5 text-[10px] text-slate-500">Belanja bahan</p>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-200/70 pt-3">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Nilai</span>
                                <span class="angka text-base font-extrabold tracking-tight text-slate-900">
                                    {{ Rupiah::format($c['total_nilai']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ================= AKTIVITAS TERBARU ================= --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            {{-- Oplosan terbaru --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div>
                        <h2 class="card-title">Laporan Oplosan Terbaru</h2>
                        <p class="card-sub">
                            {{ $aktivitasOplosan->lastPage() > 1 ? 'Halaman ' . $aktivitasOplosan->currentPage() . ' dari ' . $aktivitasOplosan->lastPage() : 'Seluruh' }}
                            catatan periode ini ({{ number_format($aktivitasOplosan->total()) }} total).
                        </p>
                    </div>
                    <a href="{{ route('laporan-oplosan.index') }}" class="text-xs font-bold text-blue-600 transition hover:text-blue-700">Lihat semua →</a>
                </div>

                @if ($aktivitasOplosan->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada laporan</p>
                        <p class="mt-1 text-xs text-slate-500">Data akan muncul setelah laporan oplosan dicatat.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($aktivitasOplosan as $laporan)
                            <li>
                                <a href="{{ route('laporan-oplosan.edit', $laporan) }}"
                                   class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-blue-50/40 sm:px-5"
                                   title="Edit laporan ini">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M4 8h16M6 6h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-slate-800">{{ \App\Support\Tanggal::panjang($laporan->tanggal) }}</p>
                                        <p class="truncate text-[11px] text-slate-500">
                                            {{ $laporan->kode_warna_unit }} · {{ $laporan->no_plat }} · {{ $laporan->rincian_bahan }}
                                        </p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="text-sm font-bold text-slate-900">{{ number_format($laporan->qty_cc) }} CC</p>
                                        <p class="text-[11px] text-slate-500">{{ $laporan->cabang_area }}</p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <x-pager :paginator="$aktivitasOplosan" anchor="aktivitas-oplosan" />
                @endif
            </div>

            {{-- Order terbaru --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div>
                        <h2 class="card-title">Nota Order Terbaru</h2>
                        <p class="card-sub">
                            {{ $aktivitasOrder->lastPage() > 1 ? 'Halaman ' . $aktivitasOrder->currentPage() . ' dari ' . $aktivitasOrder->lastPage() : 'Seluruh' }}
                            nota periode ini ({{ number_format($aktivitasOrder->total()) }} total).
                        </p>
                    </div>
                    <a href="{{ route('riwayat-order.index') }}" class="text-xs font-bold text-blue-600 transition hover:text-blue-700">Lihat semua →</a>
                </div>

                @if ($aktivitasOrder->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada order</p>
                        <p class="mt-1 text-xs text-slate-500">Data akan muncul setelah pembelian dicatat.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($aktivitasOrder as $nota)
                            @php $order = $nota['items']->first(); @endphp
                            <li>
                                <a href="{{ route('riwayat-order.edit', $order) }}"
                                   class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-indigo-50/50 sm:px-5"
                                   title="Buka item pertama nota ini">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ \App\Support\Tanggal::panjang($order->tanggal) }}</p>
                                    <p class="truncate text-[11px] text-slate-500">
                                        {{ $order->no_bukti_faktur }} · {{ $nota['jumlahItem'] }} item · {{ $order->nama_barang }}@if ($nota['jumlahItem'] > 1), dll.@endif
                                    </p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="angka-sel text-sm">{{ Rupiah::format($nota['total']) }}</p>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $order->cabang_area }}
                                    </p>
                                </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <x-pager :paginator="$aktivitasOrder" anchor="aktivitas-order" />
                @endif
            </div>
        </div>

        {{-- ================= AKTIVITAS LAPORAN HARIAN & SURAT JALAN ================= --}}
        {{-- Dua kartu ini disusun berpasangan seperti kartu oplosan & order di
             atas, jadi dashboard menampilkan keempat modul secara lengkap.
             Penomoran halamannya terpisah (hal_harian & hal_surat) supaya
             membuka halaman salah satunya tidak menggeser yang lain. --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            {{-- Laporan harian oplosan terbaru --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div>
                        <h2 class="card-title">Laporan Harian Terbaru</h2>
                        <p class="card-sub">
                            {{ $aktivitasHarian->lastPage() > 1 ? 'Halaman ' . $aktivitasHarian->currentPage() . ' dari ' . $aktivitasHarian->lastPage() : 'Seluruh' }}
                            tanggal kerja periode ini ({{ number_format($aktivitasHarian->total()) }} total).
                        </p>
                    </div>
                    <a href="{{ route('laporan-harian.index') }}" class="text-xs font-bold text-emerald-600 transition hover:text-emerald-700">Lihat semua →</a>
                </div>

                @if ($aktivitasHarian->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada laporan harian</p>
                        <p class="mt-1 text-xs text-slate-500">Data akan muncul setelah pekerjaan oplosan harian dicatat.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($aktivitasHarian as $hari)
                            @php
                                $barisHari = $hari['items'];
                                $samaHari = $barisHari->where('hasil_matching', 'Sama')->count();
                            @endphp
                            <li>
                                <a href="{{ route('laporan-harian.index', ['bulan' => $hari['tanggal']?->format('m'), 'tahun' => $hari['tanggal']?->format('Y')]) }}"
                                   class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-emerald-50/40 sm:px-5"
                                   title="Buka laporan harian tanggal ini">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-slate-800">{{ \App\Support\Tanggal::panjang($hari['tanggal']) }}</p>
                                        <p class="truncate text-[11px] text-slate-500">
                                            {{ $barisHari->count() }} pekerjaan · {{ $barisHari->pluck('plat_nomor')->filter()->unique()->take(2)->implode(', ') }}@if ($barisHari->pluck('plat_nomor')->filter()->unique()->count() > 2), dll.@endif
                                        </p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="text-sm font-bold text-slate-900">{{ number_format($barisHari->sum('volume_cc')) }} cc</p>
                                        <p class="text-[11px] text-slate-500">
                                            {{ $barisHari->count() > 0 ? round($samaHari / $barisHari->count() * 100) : 0 }}% matching sama
                                        </p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <x-pager :paginator="$aktivitasHarian" anchor="aktivitas-harian" />
                @endif
            </div>

            {{-- Surat jalan terbaru --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div>
                        <h2 class="card-title">Surat Jalan Terbaru</h2>
                        <p class="card-sub">
                            {{ $aktivitasSurat->lastPage() > 1 ? 'Halaman ' . $aktivitasSurat->currentPage() . ' dari ' . $aktivitasSurat->lastPage() : 'Seluruh' }}
                            surat periode ini ({{ number_format($aktivitasSurat->total()) }} total).
                        </p>
                    </div>
                    <a href="{{ route('surat-jalan.index') }}" class="text-xs font-bold text-rose-600 transition hover:text-rose-700">Lihat semua →</a>
                </div>

                @if ($aktivitasSurat->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada surat jalan</p>
                        <p class="mt-1 text-xs text-slate-500">Data akan muncul setelah barang keluar gudang dicatat.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($aktivitasSurat as $surat)
                            @php $barangPertama = $surat['items']->first(); @endphp
                            <li>
                                <a href="{{ route('surat-jalan.index') }}"
                                   class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-rose-50/40 sm:px-5"
                                   title="Buka halaman surat jalan">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-slate-800">{{ $surat['nomor'] ?? 'Tanpa nomor' }}</p>
                                        <p class="truncate text-[11px] text-slate-500">
                                            {{ \App\Support\Tanggal::panjang($surat['tanggal']) }} · {{ $barangPertama->nama_barang }}@if ($surat['jumlahItem'] > 1), dll.@endif
                                        </p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="text-sm font-bold text-slate-900">{{ number_format($surat['total']) }} <span class="text-[11px] font-semibold text-slate-400">barang</span></p>
                                        <p class="text-[11px] text-slate-500">{{ $barangPertama->cabang_area }}</p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <x-pager :paginator="$aktivitasSurat" anchor="aktivitas-surat" />
                @endif
            </div>
        </div>

        <p class="pb-2 px-1 text-center text-[11px] text-slate-400">
            @if ($semuaPeriode)
                Menampilkan data dari seluruh periode yang tercatat.
            @else
                Statistik dihitung dari data periode {{ $labelPeriode }}.
            @endif
        </p>
    </div>
@endsection
