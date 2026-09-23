@extends('layouts.app')

@section('title', 'Dashboard')

@php
    use App\Support\Cabang;
@endphp
@section('content')
    @php
        $namaBulan = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
        if ($bulan) {
            $labelPeriode = \Illuminate\Support\Carbon::create(null, (int) $bulan, 1)->translatedFormat('F') . ' / ' . $tahun;
        } else {
            $labelPeriode = $tahun ? 'Tahun ' . $tahun : 'Semua Periode';
        }
        $labelPeriode .= $cabang ? ' · ' . $cabang : '';

        // Skala grafik tren: ambil nilai tertinggi dari kedua seri.
        $maksTren = collect($tren)->max(fn ($b) => max($b['oplosan'], $b['order'])) ?: 1;
        $totalKeseluruhan = $totalOplosan + $totalOrder;
    @endphp

    <div class="space-y-5">
        {{-- ================= HEADER HALAMAN ================= --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="pill pill-blue mb-2">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-600"></span>
                    Ringkasan Statistik
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Dashboard</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Pantau rekap oplosan & belanja bahan dalam satu tampilan.
                    <span class="font-semibold text-slate-600">Periode: {{ $labelPeriode }}</span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <a href="{{ route('laporan-oplosan.index') }}" class="btn btn-outline flex-1 sm:flex-none">
                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 3v2m6-2v2M4 8h16M6 6h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                    </svg>
                    Laporan Oplosan
                </a>
                <a href="{{ route('riwayat-order.index') }}" class="btn btn-outline flex-1 sm:flex-none">
                    <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Riwayat Order
                </a>
                @if (auth()->user()->role === 'tinter')
                    <a href="{{ route('riwayat-order.create') }}" class="btn btn-primary w-full sm:w-auto">
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
                        'sub'   => 'Rp ' . number_format($totalBiayaOplosan, 0, ',', '.') . ' total biaya',
                        'warna' => 'from-blue-500 to-blue-600',
                        'glow'  => 'bg-blue-500/10 group-hover:bg-blue-500/20',
                        'icon'  => 'M9 17V7m6 10V7M4 4h16v16H4z',
                    ],
                    [
                        'label' => 'Total Qty Oplosan',
                        'nilai' => number_format($totalCc) . ' CC',
                        'sub'   => 'Rata-rata Rp ' . number_format($rataOplosan, 0, ',', '.') . ' / laporan',
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
                        'nilai' => 'Rp ' . number_format($totalBelanja, 0, ',', '.'),
                        'sub'   => 'Rata-rata Rp ' . number_format($rataOrder, 0, ',', '.') . ' / item',
                        'warna' => 'from-emerald-400 to-emerald-600',
                        'glow'  => 'bg-emerald-500/10 group-hover:bg-emerald-500/20',
                        'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v-2',
                    ],
                ];
            @endphp

            @foreach ($kartuUtama as $kartu)
                <div class="card card-hover group relative overflow-hidden p-5">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full {{ $kartu['glow'] }} blur-2xl transition-all"></div>
                    <div class="relative z-10 flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $kartu['warna'] }} text-white shadow-lg">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kartu['icon'] }}"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ $kartu['label'] }}</p>
                            <p class="mt-1 truncate text-2xl font-bold tracking-tight text-slate-900">{{ $kartu['nilai'] }}</p>
                            <p class="mt-0.5 truncate text-xs text-slate-500">{{ $kartu['sub'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================= FILTER PERIODE ================= --}}
        {{-- Ikut alur halaman (tanpa sticky), disamakan dengan halaman
             manajer: panel menempel bikin konten di bawahnya tertutup. --}}
        <div class="filter-sticky">
            <div class="flex flex-col gap-3 p-3 sm:p-4 xl:flex-row xl:items-center xl:gap-4">
                {{-- Judul + jumlah data --}}
                <div class="flex shrink-0 items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-800 text-white shadow-md shadow-slate-800/20">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L15 12.414V19l-6 3v-9.586L3.293 6.707A1 1 0 0 1 3 6V4Z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold leading-tight text-slate-900">Filter Periode</p>
                        <p class="text-[11px] leading-tight text-slate-500">
                            {{ number_format($totalKeseluruhan) }} data
                            @if ($cabang)
                                &middot; {{ $cabang }}
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Chip periode cepat --}}
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
                <div class="flex shrink-0 flex-wrap items-center gap-2">
                    @foreach ($chips as $chip)
                        <a href="{{ $chip['url'] }}" class="chip {{ $chip['aktif'] ? 'active' : '' }}">{{ $chip['label'] }}</a>
                    @endforeach
                </div>

                {{-- Form filter: satu baris di layar lebar --}}
                <form method="GET" action="{{ route('dashboard') }}"
                      class="filter-baris flex-1 items-end">
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
                            <p class="card-sub">Jumlah laporan oplosan vs item order per bulan.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-500">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span> Oplosan
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full bg-indigo-400"></span> Order
                        </span>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="flex h-52 items-end justify-between gap-2 sm:gap-4">
                        @foreach ($tren as $baris)
                            @php
                                $tinggiOplosan = round(($baris['oplosan'] / $maksTren) * 100);
                                $tinggiOrder = round(($baris['order'] / $maksTren) * 100);
                            @endphp
                            <div class="group flex h-full flex-1 flex-col items-center justify-end gap-2">
                                <div class="flex h-full w-full items-end justify-center gap-1 sm:gap-1.5">
                                    <div class="relative flex h-full w-1/2 max-w-[26px] items-end">
                                        <div class="w-full rounded-t-lg bg-gradient-to-t from-blue-600 to-blue-400 transition-all duration-500 group-hover:from-blue-700"
                                             style="height: {{ max($tinggiOplosan, 2) }}%"
                                             title="{{ $baris['label'] }} · {{ $baris['oplosan'] }} laporan oplosan ({{ number_format($baris['cc']) }} CC)"></div>
                                    </div>
                                    <div class="relative flex h-full w-1/2 max-w-[26px] items-end">
                                        <div class="w-full rounded-t-lg bg-gradient-to-t from-indigo-500 to-indigo-300 transition-all duration-500"
                                             style="height: {{ max($tinggiOrder, 2) }}%"
                                             title="{{ $baris['label'] }} · {{ $baris['order'] }} item order (Rp {{ number_format($baris['belanja'], 0, ',', '.') }})"></div>
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
                            ['label' => 'Belanja bulan ini', 'nilai' => 'Rp ' . number_format($ringkasBulanIni['belanja'], 0, ',', '.'), 'warna' => 'text-emerald-600'],
                            ['label' => 'Item order masuk', 'nilai' => number_format($ringkasBulanIni['order']) . ' item', 'warna' => 'text-indigo-600'],
                            ['label' => 'Laporan oplosan', 'nilai' => number_format($ringkasBulanIni['oplosan']) . ' laporan', 'warna' => 'text-blue-600'],
                            ['label' => 'Total qty oplosan', 'nilai' => number_format($ringkasBulanIni['cc']) . ' CC', 'warna' => 'text-amber-600'],
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
                                    <p class="text-[11px] text-slate-500">Rp {{ number_format($barang['belanja'], 0, ',', '.') }}</p>
                                </div>
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
                                    <p class="text-[11px] text-slate-500">{{ number_format($warna['unit']) }} unit · Rp {{ number_format($warna['biaya'], 0, ',', '.') }}</p>
                                </div>
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
                                    <p class="text-[11px] font-semibold text-blue-700">Rp {{ number_format($c['nilai_oplosan'], 0, ',', '.') }}</p>
                                    <p class="mt-0.5 text-[10px] text-slate-500">{{ number_format($c['cc_oplosan']) }} CC</p>
                                </div>

                                {{-- Kolom Order --}}
                                <div class="rounded-xl border-indigo-100 bg-indigo-50/50 p-3">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-700">Order</p>
                                    <p class="mt-1 text-lg font-bold tracking-tight text-slate-900">
                                        {{ number_format($c['nota_order']) }} <span class="text-[11px] font-semibold text-slate-400">nota</span>
                                    </p>
                                    <p class="text-[11px] font-semibold text-indigo-700">Rp {{ number_format($c['nilai_order'], 0, ',', '.') }}</p>
                                    <p class="mt-0.5 text-[10px] text-slate-500">Belanja bahan</p>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-200/70 pt-3">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Nilai</span>
                                <span class="text-base font-bold tracking-tight text-slate-900">
                                    Rp {{ number_format($c['total_nilai'], 0, ',', '.') }}
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
                        <p class="card-sub">5 catatan terakhir pada periode ini.</p>
                    </div>
                    <a href="{{ route('laporan-oplosan.index') }}" class="text-xs font-bold text-blue-600 transition hover:text-blue-700">Lihat semua →</a>
                </div>

                @if ($oplosanTerbaru->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada laporan</p>
                        <p class="mt-1 text-xs text-slate-500">Data akan muncul setelah laporan oplosan dicatat.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($oplosanTerbaru as $laporan)
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
                                        <p class="truncate text-sm font-semibold text-slate-800">{{ $laporan->kode_warna_unit }}</p>
                                        <p class="truncate text-[11px] text-slate-500">
                                            {{ $laporan->no_plat }} · {{ $laporan->rincian_bahan }}
                                        </p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="text-sm font-bold text-slate-900">{{ number_format($laporan->qty_cc) }} CC</p>
                                        <p class="text-[11px] text-slate-500">{{ $laporan->tanggal?->format('d/m/Y') }}</p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Order terbaru --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div>
                        <h2 class="card-title">Nota Order Terbaru</h2>
                        <p class="card-sub">5 nota terakhir pada periode ini, lengkap dengan jumlah itemnya.</p>
                    </div>
                    <a href="{{ route('riwayat-order.index') }}" class="text-xs font-bold text-blue-600 transition hover:text-blue-700">Lihat semua →</a>
                </div>

                @if ($orderTerbaru->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada order</p>
                        <p class="mt-1 text-xs text-slate-500">Data akan muncul setelah pembelian dicatat.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($orderTerbaru as $nota)
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
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $order->no_bukti_faktur }}</p>
                                    <p class="truncate text-[11px] text-slate-500">
                                        {{ $nota['jumlahItem'] }} item · {{ $order->nama_barang }}@if ($nota['jumlahItem'] > 1), dll.@endif
                                    </p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-bold text-slate-900">Rp {{ number_format($nota['total'], 0, ',', '.') }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $order->tanggal?->format('d/m/Y') }}</p>
                                </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
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
