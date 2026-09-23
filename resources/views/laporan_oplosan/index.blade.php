@extends('layouts.app')

@section('title', 'Laporan Oplosan')

@php
    use App\Support\Cabang;
@endphp
@section('content')
    <div class="space-y-5">
        {{-- ================= HEADER HALAMAN ================= --}}
        <div class="relative overflow-hidden rounded-3xl border-white/60 bg-gradient-to-br from-sky-600 via-blue-600 to-indigo-700 p-6 text-white shadow-xl shadow-blue-500/25 sm:p-7">
            <div class="animate-floaty pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-white/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-16 -left-10 h-48 w-48 rounded-full bg-sky-200/25 blur-3xl"></div>

            <div class="relative z-10 flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-wider ring-1 ring-white/25 backdrop-blur">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-sky-200"></span>
                        Manajemen Oplosan
                    </div>
                    <h1 class="mt-3 text-2xl font-bold tracking-tight sm:text-3xl">Laporan Oplosan</h1>
                    <p class="mt-1.5 max-w-xl text-sm text-blue-50/90">
                        Rekap tinting cat per unit, dipisah per cabang dengan total nilai tiap nota.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <a href="{{ route('laporan-oplosan.preview', request()->query()) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold ring-1 ring-white/25 backdrop-blur transition hover:bg-white/25">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a2 2 0 0 1 1.414.586l5.414 5.414A2 2 0 0 1 19 10.414V19a2 2 0 0 1-2 2Z"/>
                        </svg>
                        Preview PDF
                    </a>
                    <a href="{{ route('laporan-oplosan.pdf', request()->query()) }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold ring-1 ring-white/25 backdrop-blur transition hover:bg-white/25">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0 4-4m-4 4-4-4M4 20h16"/>
                        </svg>
                        Download
                    </a>
                    <a href="{{ route('laporan-oplosan.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-blue-700 shadow-lg transition hover:-translate-y-0.5 hover:bg-blue-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Laporan
                    </a>
                </div>
            </div>
        </div>

        {{-- ================= STATISTIK ================= --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $statistik = [
                    [
                        'label' => 'Total Nota',
                        'nilai' => number_format($totalNota),
                        'sub'   => number_format($totalOplosan) . ' item bahan',
                        'warna' => 'from-blue-500 to-blue-600',
                        'glow'  => 'bg-blue-500/10 group-hover:bg-blue-500/20',
                        'icon'  => 'M9 17V7m6 10V7M4 4h16v16H4z',
                    ],
                    [
                        'label' => 'Total Nilai Nota',
                        'nilai' => 'Rp ' . number_format($totalNilaiNota, 0, ',', '.'),
                        'sub'   => 'Rata-rata Rp ' . number_format($rataPerNota, 0, ',', '.') . ' / nota',
                        'warna' => 'from-emerald-400 to-emerald-600',
                        'glow'  => 'bg-emerald-500/10 group-hover:bg-emerald-500/20',
                        'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v-2',
                    ],
                    [
                        'label' => 'Total Qty (CC/LTR)',
                        'nilai' => number_format($totalCc),
                        'sub'   => 'Pemakaian bahan tinting',
                        'warna' => 'from-amber-400 to-orange-500',
                        'glow'  => 'bg-amber-500/10 group-hover:bg-amber-500/20',
                        'icon'  => 'M5 13l4 4L19 7',
                    ],
                    [
                        'label' => 'Total Biaya Item',
                        'nilai' => 'Rp ' . number_format($totalBiaya, 0, ',', '.'),
                        'sub'   => $perCabang->count() . ' cabang tercatat',
                        'warna' => 'from-violet-500 to-fuchsia-600',
                        'glow'  => 'bg-violet-500/10 group-hover:bg-violet-500/20',
                        'icon'  => 'M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6',
                    ],
                ];
            @endphp
            @foreach ($statistik as $kartu)
                <div class="card card-hover group relative overflow-hidden p-5">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full {{ $kartu['glow'] }} blur-2xl transition-all"></div>
                    <div class="relative z-10 flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $kartu['warna'] }} text-white shadow-lg">
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

        {{-- ================= RINCIAN PER CABANG ================= --}}
        @if ($perCabang->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white shadow-md shadow-sky-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Rincian per Cabang</h2>
                            <p class="card-sub">Total nota, qty &amp; biaya tinting tiap cabang pada periode ini.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                    @foreach ($perCabang as $c)
                        @php $persen = $totalNilaiNota > 0 ? round(($c['nilai'] / $totalNilaiNota) * 100) : 0; @endphp
                        <div class="group relative overflow-hidden rounded-2xl border-slate-200/70 bg-gradient-to-br from-white to-slate-50/80 p-4 transition hover:-translate-y-0.5 hover:border-sky-200 hover:shadow-lg">
                            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-sky-500/5 blur-2xl transition group-hover:bg-sky-500/10"></div>
                            <div class="relative z-10">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-800">{{ $c['cabang'] }}</p>
                                        <p class="mt-0.5 text-[11px] text-slate-500">
                                            {{ number_format($c['nota']) }} nota &middot; {{ number_format($c['item']) }} item
                                            &middot; {{ number_format($c['cc']) }} CC
                                        </p>
                                    </div>
                                    <span class="pill pill-blue shrink-0">{{ $persen }}%</span>
                                </div>

                                <p class="mt-3 text-xl font-bold tracking-tight text-slate-900">
                                    Rp {{ number_format($c['nilai'], 0, ',', '.') }}
                                </p>

                                <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-blue-600"
                                         style="width: {{ max($persen, 2) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ================= FILTER PERIODE ================= --}}
        {{-- Ikut alur halaman (tanpa sticky), disamakan dengan halaman
             manajer: panel menempel bikin konten di bawahnya tertutup. --}}
        <div class="filter-sticky">
            <div class="flex flex-col gap-3 p-3 sm:p-4 xl:flex-row xl:items-end xl:gap-4">
                {{-- Judul + jumlah nota --}}
                <div class="flex shrink-0 items-center gap-3 xl:pb-1">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-800 text-white shadow-md shadow-slate-800/20">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L15 12.414V19l-6 3v-9.586L3.293 6.707A1 1 0 0 1 3 6V4Z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold leading-tight text-slate-900">Filter Periode</p>
                        <p class="text-[11px] leading-tight text-slate-500">
                            {{ number_format($notas->total()) }} nota
                            @if ($cabang)
                                &middot; {{ $cabang }}
                            @endif
                        </p>
                    </div>
                </div>

                <form method="GET" action="{{ route('laporan-oplosan.index') }}"
                      class="filter-baris flex-1 items-end">
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
                        <a href="{{ route('laporan-oplosan.index') }}" class="btn btn-ghost h-[42px] shrink-0">Reset</a>
                    @endif
                </div>
            </form>
            </div>
        </div>

        {{-- ================= DAFTAR NOTA ================= --}}
        <div class="card overflow-hidden">
            <div class="card-head flex">
                <div>
                    <h2 class="card-title">Daftar Nota &amp; Item</h2>
                    <p class="card-sub">
                        Klik satu nota untuk membuka rincian bahannya. Nilai di kanan adalah
                        <span class="font-semibold text-slate-600">total satu nota</span>, bukan per item.
                    </p>
                </div>
                <span class="pill pill-slate">{{ number_format($notas->total()) }} Nota</span>
            </div>

            @if ($notas->isEmpty())
                {{-- Keadaan kosong --}}
                <div class="px-6 py-14">
                    <div class="mx-auto flex max-w-sm flex-col items-center text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 shadow-inner">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Belum ada laporan</h3>
                        <p class="mt-1 text-sm text-slate-500">Tidak ada data untuk filter yang dipilih.</p>
                        <a href="{{ route('laporan-oplosan.create') }}" class="btn btn-primary mt-5">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Laporan Pertama
                        </a>
                    </div>
                </div>
            @else
                {{-- Satu nota = satu kartu berisi rincian bahannya.
                     Item per nota bisa dibuka-tutup dan tetap bisa diubah
                     lewat tombol Edit / Hapus per item. --}}
                <div class="divide-y divide-slate-100">
                    @foreach ($notas as $nota)
                        @include('laporan_oplosan._nota', [
                            'nota' => $nota,
                            'jumlahItem' => $nota['jumlahItem'],
                            'bisaAksi' => true,
                        ])
                    @endforeach
                </div>

            @endif
            @if ($notas->hasPages())
                <div class="border-t border-slate-200/60 px-3 py-4 sm:px-6">
                    {{ $notas->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
