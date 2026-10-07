@extends('layouts.app')

@section('title', 'Surat Jalan')

@php
    use App\Support\Cabang;
@endphp

@section('content')
<div class="space-y-5">
    {{-- ================= HEADER HALAMAN ================= --}}
    <div class="page-head">
        <div>
            <div class="pill pill-blue mb-2">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-600"></span>
                Barang Keluar Gudang
            </div>
            <h1 class="page-title">Surat Jalan</h1>
            <p class="page-sub">Rekap pengiriman barang: nomor surat, tujuan, dan daftar barang yang dikirim.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('surat-jalan.preview', request()->query()) }}" class="btn btn-outline btn-sm">
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a2 2 0 0 1 1.414.586l5.414 5.414A2 2 0 0 1 19 10.414V19a2 2 0 0 1-2 2Z"/>
                </svg>
                Preview PDF
            </a>
            <a href="{{ route('surat-jalan.pdf', request()->query()) }}" class="btn btn-outline btn-sm">
                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0 4-4m-4 4-4-4M4 20h16"/>
                </svg>
                Download
            </a>
            <a href="{{ route('surat-jalan.create') }}" class="btn btn-primary btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Surat
            </a>
        </div>
    </div>

    {{-- ================= STATISTIK ================= --}}
    @php
        $statistik = [
            [
                'label' => 'Total Surat',
                'nilai' => number_format($totalSurat),
                'sub'   => number_format($totalBaris) . ' barang tercatat',
                'warna' => 'from-blue-500 to-blue-600',
                'glow'  => 'bg-blue-500/10 group-hover:bg-blue-500/20',
                'icon'  => 'M9 17V7m6 10V7M4 4h16v16H4z',
            ],
            [
                'label' => 'Total Barang',
                'nilai' => number_format($totalBaris),
                'sub'   => $perBarang->count() . ' jenis barang berbeda',
                'warna' => 'from-amber-400 to-orange-500',
                'glow'  => 'bg-amber-500/10 group-hover:bg-amber-500/20',
                'icon'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            ],
            [
                'label' => 'Total Jumlah Dikirim',
                'nilai' => number_format($totalJumlah),
                'sub'   => 'rata-rata ' . number_format($rataPerSurat, 1) . ' per surat',
                'warna' => 'from-violet-500 to-fuchsia-600',
                'glow'  => 'bg-violet-500/10 group-hover:bg-violet-500/20',
                'icon'  => 'M3 3v18h18M7 14l3-3 3 3 5-6',
            ],
            [
                'label' => 'Jenis Barang',
                'nilai' => number_format($perBarang->count()),
                'sub'   => 'jenis barang berbeda dikirim',
                'warna' => 'from-emerald-400 to-emerald-600',
                'glow'  => 'bg-emerald-500/10 group-hover:bg-emerald-500/20',
                'icon'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            ],
        ];
    @endphp
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
                        <p class="mt-1 truncate text-xs text-slate-500">{{ $kartu['sub'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ================= BARANG TERBANYAK DIKIRIM ================= --}}
    @if ($perBarang->isNotEmpty())
        <div class="card overflow-hidden">
            <div class="card-head flex">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-md shadow-amber-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="card-title">Barang Paling Sering Dikirim</h2>
                        <p class="card-sub">Barang dengan jumlah pengiriman terbesar pada periode ini.</p>
                    </div>
                </div>
                <span class="pill pill-slate">Top {{ $perBarang->count() }}</span>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($perBarang as $b)
                    <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-5">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $b['barang'] }}</p>
                            <p class="text-[11px] text-slate-500">{{ number_format($b['kali']) }} kali dikirim</p>
                        </div>
                        <span class="angka shrink-0 text-sm font-extrabold text-slate-900">{{ number_format($b['jumlah']) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ================= FILTER PERIODE ================= --}}
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
                    {{ number_format($totalSurat) }} surat
                    @if ($cabang)
                        &middot; {{ $cabang }}
                    @endif
                </p>
            </div>
        </div>

        <div class="filter-panel-body">
            <form method="GET" action="{{ route('surat-jalan.index') }}" class="filter-baris items-end">
                <div>
                    <label for="bulan" class="label">Bulan</label>
                    <div class="relative">
                        <select id="bulan" name="bulan" class="input appearance-none pr-10">
                            <option value="">Semua Bulan</option>
                            @foreach (['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $val => $label)
                                <option value="{{ $val }}" {{ (string) $bulan === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="tahun" class="label">Tahun</label>
                    <input id="tahun" type="number" name="tahun" value="{{ $tahun }}" min="2000" max="2100" placeholder="Semua" class="input">
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
                    <button type="submit" class="btn btn-dark h-[42px] flex-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"/>
                        </svg>
                        Terapkan
                    </button>
                    @if ($bulan || $tahun || $cabang)
                        <a href="{{ route('surat-jalan.index') }}" class="btn btn-ghost h-[42px] shrink-0">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ================= DAFTAR SURAT ================= --}}
    <div class="card overflow-hidden">
        <div class="card-head flex">
            <div>
                <h2 class="card-title">Daftar Surat &amp; Barang</h2>
                <p class="card-sub">
                    Klik satu surat untuk membuka rincian barangnya. Angka di kanan adalah
                    <span class="font-semibold text-slate-600">total jumlah barang</span> dalam surat itu.
                </p>
            </div>
            <span class="pill pill-slate">
                {{ number_format($suratHalaman->total()) }} Surat
                @if ($suratHalaman->lastPage() > 1)
                    &middot; hal. {{ $suratHalaman->currentPage() }}/{{ $suratHalaman->lastPage() }}
                @endif
            </span>
        </div>

        @if ($suratHalaman->isEmpty())
            <div class="px-6 py-14">
                <div class="mx-auto flex max-w-sm flex-col items-center text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 shadow-inner">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Belum ada surat jalan</h3>
                    <p class="mt-1 text-sm text-slate-500">Tidak ada data untuk filter yang dipilih.</p>
                    <a href="{{ route('surat-jalan.create') }}" class="btn btn-primary mt-5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Surat Pertama
                    </a>
                </div>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($suratHalaman as $surat)
                    @include('surat_jalan._surat', [
                        'surat' => $surat,
                        'jumlahItem' => $surat['jumlahItem'],
                        'bisaAksi' => auth()->check() && auth()->user()->role === 'tinter',
                    ])
                @endforeach
            </div>

            <x-pager :paginator="$suratHalaman" anchor="daftar-surat" />
        @endif
    </div>
</div>
@endsection
