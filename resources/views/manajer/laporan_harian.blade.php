@extends('layouts.manajer')

@section('title', 'Laporan Harian - Mode Pantau')

@section('content')
    @php
        use App\Support\Cabang;

        // Kartu statistik disusun di sini supaya markup-nya sama persis
        // dengan halaman pantau lain (mode manajer).
        $statistik = [
            [
                'label' => 'Total Pekerjaan',
                'nilai' => number_format($jumlahBaris),
                'sub'   => 'baris laporan harian periode ini',
                'warna' => 'from-blue-500 to-blue-600',
                'glow'  => 'bg-blue-500/10',
                'icon'  => 'M9 17V7m6 10V7M4 4h16v16H4z',
            ],
            [
                'label' => 'Total Volume',
                'nilai' => number_format($totalVolume) . ' cc',
                'sub'   => 'pemakaian bahan oplosan',
                'warna' => 'from-amber-400 to-orange-500',
                'glow'  => 'bg-amber-500/10',
                'icon'  => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2',
            ],
            [
                'label' => 'Rata-rata Durasi',
                'nilai' => $rataDurasi > 0 ? $rataDurasi . ' mnt' : '—',
                'sub'   => $totalDurasi > 0
                    ? number_format($totalDurasi / 60, 1) . ' jam kerja oplos tercatat'
                    : 'durasi jam belum diisi',
                'warna' => 'from-violet-500 to-fuchsia-600',
                'glow'  => 'bg-violet-500/10',
                'icon'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            [
                // Angka ini yang menunjukkan mutu kerja tinter.
                'label' => 'Matching Sama',
                'nilai' => $persenSama . '%',
                'sub'   => number_format($jumlahSama) . ' dari ' . number_format($jumlahBaris) . ' pekerjaan',
                'warna' => 'from-emerald-400 to-emerald-600',
                'glow'  => 'bg-emerald-500/10',
                'icon'  => 'M5 13l4 4L19 7',
            ],
        ];
    @endphp

    <div class="space-y-5">
        {{-- ================= HEADER ================= --}}
        <div class="page-head">
            <div>
                <div class="pill pill-emerald mb-2">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-600"></span>
                    Mode Pantau
                </div>
                <h1 class="page-title">Laporan Harian (Pantau)</h1>
                <p class="page-sub">Periksa mutu kerja oplosan harian: hasil matching warna, lama pengerjaan, dan unit yang perlu ditelusuri. Tanpa aksi ubah data.</p>
            </div>

            <div class="page-actions">
                <a href="{{ route('laporan-harian.pdf', request()->query()) }}" class="btn btn-outline btn-sm">
                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a2 2 0 0 1 1.414.586l5.414 5.414A2 2 0 0 1 19 10.414V19a2 2 0 0 1-2 2Z"/>
                    </svg>
                    Unduh PDF
                </a>
                <a href="{{ route('manajer.dashboard', request()->query()) }}" class="btn btn-outline btn-sm">
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0 7-7m-7 7h18"/>
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>

        {{-- ================= STATISTIK ================= --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($statistik as $kartu)
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

        {{-- ================= PERLU DITELUSURI ================= --}}
        @if ($belumSama->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-orange-500 text-white shadow-md shadow-rose-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Perlu Ditelusuri</h2>
                            <p class="card-sub">Hasil matching warnanya belum "Sama" - periksa ulang pengerjaannya.</p>
                        </div>
                    </div>
                    <span class="pill pill-rose">{{ $belumSama->count() }} Catatan</span>
                </div>

                <ul class="divide-y divide-slate-100/70">
                    @foreach ($belumSama as $item)
                        @php
                            $warnaMatching = [
                                'Mirip' => 'pill-amber',
                                'Beda'  => 'pill-rose',
                            ][$item->hasil_matching] ?? 'pill-slate';
                        @endphp
                        <li class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-rose-50/40 sm:px-5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01"/>
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-800">
                                    {{ $item->plat_nomor }}
                                    <span class="font-normal text-slate-400">&middot; {{ $item->tipe_mobil }}</span>
                                </p>
                                <p class="truncate text-[11px] text-slate-500">
                                    {{ $item->kode_warna }} &middot; {{ $item->bahan_cat }}
                                    &middot; {{ number_format($item->volume_cc) }} cc
                                    @if ($item->cabang_area)
                                        &middot; {{ $item->cabang_area }}
                                    @endif
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="{{ $warnaMatching }} pill">{{ $item->hasil_matching }}</span>
                                <p class="mt-1 text-[11px] text-slate-500">{{ \App\Support\Tanggal::pendek($item->tanggal) }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ================= UNIT TERSIBUK ================= --}}
        @if ($perUnit->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-md shadow-slate-800/25">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13l1.5-4.5A2 2 0 0 1 6.4 7h11.2a2 2 0 0 1 1.9 1.5L21 13m-18 0v5a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1h12v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-5m-18 0h18M7 16h.01M17 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Unit Tersibuk</h2>
                            <p class="card-sub">Plat dengan volume pemakaian bahan terbesar pada periode ini.</p>
                        </div>
                    </div>
                    <span class="pill pill-slate">Top {{ $perUnit->count() }}</span>
                </div>

                <ul class="divide-y divide-slate-100/70">
                    @foreach ($perUnit as $index => $unit)
                        <li class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-slate-50/60 sm:px-5">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold {{ $index === 0 ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $index + 1 }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $unit['plat'] }}</p>
                                <p class="truncate text-[11px] text-slate-500">
                                    {{ $unit['warna'] }} &middot; {{ $unit['jumlah'] }} pekerjaan
                                    @if ($unit['durasi'] > 0)
                                        &middot; {{ $unit['durasi'] }} mnt
                                    @endif
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="angka text-sm font-extrabold text-slate-900">{{ number_format($unit['volume']) }} cc</p>
                                <p class="text-[11px] font-semibold {{ $unit['persen'] >= 100 ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $unit['persen'] }}% matching sama
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ================= RINCIAN PER CABANG ================= --}}
        @if ($perCabang->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-md shadow-slate-800/25">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Rincian per Cabang</h2>
                            <p class="card-sub">Jumlah pekerjaan &amp; volume pemakaian bahan tiap cabang.</p>
                        </div>
                    </div>
                </div>

                @php $totalVolumeAman = $totalVolume > 0 ? $totalVolume : 1; @endphp
                <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                    @foreach ($perCabang as $c)
                        @php $persen = round(($c['volume'] / $totalVolumeAman) * 100); @endphp
                        <div class="group relative overflow-hidden rounded-2xl border-slate-200/70 bg-gradient-to-br from-white to-slate-50/80 p-4 transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-lg">
                            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-emerald-500/5 blur-2xl transition group-hover:bg-emerald-500/10"></div>
                            <div class="relative z-10">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-800">{{ $c['cabang'] }}</p>
                                        <p class="mt-0.5 text-[11px] text-slate-500">{{ number_format($c['baris']) }} pekerjaan</p>
                                    </div>
                                    <span class="pill pill-emerald shrink-0">{{ $persen }}%</span>
                                </div>

                                <p class="angka mt-3 text-xl font-extrabold tracking-tight text-slate-900">
                                    {{ number_format($c['volume']) }} cc
                                </p>

                                <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-600"
                                         style="width: {{ max($persen, 2) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ================= FILTER ================= --}}
        <div class="card overflow-hidden">
            <div class="card-head flex">
                <div class="flex items-center gap-3">
                    <div class="icon-badge bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-md shadow-slate-800/20">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L15 12.414V19l-6 3v-9.586L3.293 6.707A1 1 0 0 1 3 6V4Z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="card-title">Filter Periode</h2>
                        <p class="card-sub">Pilih periode untuk mempersempit data yang diperiksa.</p>
                    </div>
                </div>
            </div>

            <div class="filter-panel-body">
                <form method="GET" action="{{ route('manajer.laporan-harian') }}" class="filter-baris items-end">
                    <div>
                        <label for="bulan" class="label">Bulan</label>
                        <div class="relative">
                            <select id="bulan" name="bulan" class="input appearance-none pr-10">
                                <option value="">Semua Bulan</option>
                                @foreach ($namaBulan as $val => $label)
                                    <option value="{{ $val }}" {{ (string) $bulan === (string) $val ? 'selected' : '' }}>{{ $label }}</option>
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

                    <div class="filter-aksi flex items-center gap-2">
                        <button type="submit" class="btn btn-dark h-[42px] flex-1 min-w-0">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"/>
                            </svg>
                            <span class="truncate">Terapkan</span>
                        </button>

                        @if ($bulan || $tahun || $cabang)
                            <a href="{{ route('manajer.laporan-harian') }}" class="btn btn-ghost h-[42px] shrink-0">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= DAFTAR PEKERJAAN ================= --}}
        <div class="card overflow-hidden">
            <div class="card-head flex">
                <div>
                    <h2 class="card-title">Catatan Harian</h2>
                    <p class="card-sub">Satu tanggal = satu blok berisi pekerjaan oplosan hari itu. Mode pantau - data tidak diubah dari sini.</p>
                </div>
                <span class="pill pill-emerald">
                    {{ number_format($jumlahHari) }} Hari
                    @if ($hari->lastPage() > 1)
                        &middot; hal. {{ $hari->currentPage() }}/{{ $hari->lastPage() }}
                    @endif
                </span>
            </div>

            @if ($hari->isEmpty())
                <div class="px-6 py-14">
                    <div class="mx-auto flex max-w-sm flex-col items-center text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 shadow-inner">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Belum ada catatan harian</h3>
                        <p class="mt-1 text-sm text-slate-500">Tidak ada data untuk periode yang dipilih.</p>
                    </div>
                </div>
            @else
                {{-- Satu tanggal = satu kartu yang bisa dibuka-tutup, isinya
                     seluruh pekerjaan hari itu. --}}
                <div class="divide-y divide-slate-100">
                    @foreach ($hari as $blok)
                        @php
                            $persenHari = $blok['jumlahItem'] > 0
                                ? round(($blok['sama'] / $blok['jumlahItem']) * 100)
                                : 0;
                            $platHari = $blok['items']->pluck('plat_nomor')->filter()->unique()->values();
                            $cabangHari = $blok['items']->pluck('cabang_area')->filter()->unique()->values();
                        @endphp
                        <div class="bg-white/70 transition hover:bg-white">
                            <div class="flex flex-wrap items-center gap-3 px-4 py-4 sm:px-5">
                                <div class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-md shadow-slate-800/25">
                                    <span class="text-base font-extrabold leading-none">{{ $blok['tanggal']?->format('d') ?? '—' }}</span>
                                    <span class="text-[9px] font-semibold uppercase leading-tight">{{ $blok['tanggal']?->translatedFormat('M') }}</span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-slate-800">{{ \App\Support\Tanggal::panjang($blok['tanggal']) }}</p>
                                    <p class="truncate text-[11px] text-slate-500">
                                        <span class="font-bold text-slate-700">{{ $blok['jumlahItem'] }} pekerjaan</span>
                                        &middot; {{ number_format($blok['volume']) }} cc
                                        @if ($platHari->isNotEmpty())
                                            &middot; {{ $platHari->take(3)->implode(' · ') }}{{ $platHari->count() > 3 ? ' +' . ($platHari->count() - 3) . ' lainnya' : '' }}
                                        @endif
                                    </p>
                                    @if ($cabangHari->isNotEmpty())
                                        <span class="mt-1.5 inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-200">
                                            <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                                            </svg>
                                            {{ $cabangHari->implode(', ') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="shrink-0 text-right">
                                    <p class="angka text-base font-extrabold text-slate-900">{{ number_format($blok['volume']) }} cc</p>
                                    <p class="text-[11px] text-slate-500">
                                        <span class="font-bold {{ $persenHari === 100 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $persenHari }}% sama</span>
                                    </p>
                                </div>
                            </div>

                            <details class="group">
                                <summary class="btn-detail cursor-pointer list-none marker:hidden">
                                    <span class="transition group-open:rotate-90">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                                        </svg>
                                    </span>
                                    <span class="group-open:hidden">Buka rincian pekerjaan ({{ $blok['jumlahItem'] }})</span>
                                    <span class="hidden group-open:inline">Tutup rincian pekerjaan</span>
                                </summary>

                                <div class="divide-y divide-slate-100 border-t border-slate-200/60 bg-slate-50/40">
                                    @foreach ($blok['items'] as $item)
                                        @php
                                            $warnaMatching = [
                                                'Sama'  => 'pill-emerald',
                                                'Mirip' => 'pill-amber',
                                                'Beda'  => 'pill-rose',
                                            ][$item->hasil_matching] ?? 'pill-slate';
                                        @endphp
                                        <div class="px-4 py-3.5 sm:px-5">
                                            <div class="flex flex-wrap items-start justify-between gap-3">
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="inline-flex items-center gap-1.5 text-sm font-bold text-slate-900">
                                                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-br from-slate-700 to-slate-900 text-[11px] font-bold text-white">
                                                                {{ $item->plat_nomor ? strtoupper(substr($item->plat_nomor, 0, 3)) : '—' }}
                                                            </span>
                                                            {{ $item->plat_nomor }}
                                                        </span>
                                                        <span class="pill pill-blue">{{ $item->kode_warna }}</span>
                                                        <span class="{{ $warnaMatching }} pill">{{ $item->hasil_matching }}</span>
                                                    </div>
                                                    <p class="mt-1.5 truncate text-xs text-slate-500">
                                                        {{ $item->tipe_mobil }}
                                                        @if ($item->cabang_area)
                                                            <span class="mx-1 text-slate-300">•</span>
                                                            {{ $item->cabang_area }}
                                                        @endif
                                                    </p>
                                                </div>

                                                <div class="text-right">
                                                    <p class="angka text-base font-extrabold text-slate-900">{{ number_format($item->volume_cc) }} cc</p>
                                                    <p class="text-[11px] text-slate-500">
                                                        {{ $item->jam_dibuat ? \Illuminate\Support\Str::of($item->jam_dibuat)->substr(0, 5) : '—' }}
                                                        @if ($item->jam_selesai)
                                                            &rarr; {{ \Illuminate\Support\Str::of($item->jam_selesai)->substr(0, 5) }}
                                                        @endif
                                                        @if ($item->durasi_menit !== null)
                                                            <span class="font-semibold text-slate-600">({{ $item->durasi_menit }} mnt)</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            <p class="mt-2.5 truncate text-xs text-slate-600">
                                                <span class="font-semibold text-slate-500">Bahan:</span> {{ $item->bahan_cat }}
                                                @if ($item->keterangan)
                                                    <span class="mx-1 text-slate-300">•</span>
                                                    <span class="text-slate-500">{{ $item->keterangan }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        </div>
                    @endforeach
                </div>

                <x-pager :paginator="$hari" anchor="daftar-pantau-harian" />
            @endif
        </div>
    </div>
@endsection
