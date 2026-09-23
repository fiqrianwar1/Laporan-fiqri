@extends('layouts.manajer')

@section('title', 'Laporan Oplosan - Mode Pantau')

@section('content')
    @php
        $rupiah = fn ($angka) => 'Rp ' . number_format((float) $angka, 0, ',', '.');
    @endphp

    <div class="space-y-5">
        {{-- ================= HEADER ================= --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="pill pill-emerald mb-2">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-600"></span>
                    Mode Pantau
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Laporan Oplosan (Pantau)</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Periksa pemakaian bahan tinting per unit. Tanpa aksi ubah data.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <span class="inline-flex items-center gap-2 rounded-xl bg-white/70 px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-200/70">
                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M4 8h16M6 6h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                    </svg>
                    {{ number_format($jumlahNota) }} nota · Rp {{ number_format($totalNilaiNota, 0, ',', '.') }}
                </span>
                <a href="{{ route('laporan-oplosan.pdf', request()->query()) }}" class="btn btn-outline flex-1 sm:flex-none">
                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a2 2 0 0 1 1.414.586l5.414 5.414A2 2 0 0 1 19 10.414V19a2 2 0 0 1-2 2Z"/>
                    </svg>
                    Unduh PDF
                </a>
                <a href="{{ route('manajer.dashboard', request()->query()) }}" class="btn btn-outline flex-1 sm:flex-none">
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0 7-7m-7 7h18"/>
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>

        {{-- ================= STATISTIK ================= --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $statistik = [
                    [
                        'label' => 'Total Laporan',
                        'nilai' => number_format($totalLaporan),
                        'sub'   => 'Catatan tinting periode ini',
                        'warna' => 'from-blue-500 to-blue-600',
                        'glow'  => 'bg-blue-500/10',
                        'icon'  => 'M9 17V7m6 10V7M4 4h16v16H4z',
                    ],
                    [
                        'label' => 'Total Qty (CC/LTR)',
                        'nilai' => number_format($totalCc),
                        'sub'   => 'Pemakaian bahan tinting',
                        'warna' => 'from-amber-400 to-orange-500',
                        'glow'  => 'bg-amber-500/10',
                        'icon'  => 'M5 13l4 4L19 7',
                    ],
                    [
                        'label' => 'Total Biaya',
                        'nilai' => $rupiah($totalBiaya),
                        'sub'   => 'Rata-rata ' . $rupiah($rataBiaya) . ' per laporan',
                        'warna' => 'from-emerald-400 to-emerald-600',
                        'glow'  => 'bg-emerald-500/10',
                        'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v-2',
                    ],
                    [
                        // Angka ini yang paling berguna untuk menilai kewajaran harga.
                        'label' => 'Biaya per CC',
                        'nilai' => $rupiah($biayaPerCc),
                        'sub'   => 'Patokan kewajaran harga tinting',
                        'warna' => 'from-indigo-500 to-violet-600',
                        'glow'  => 'bg-indigo-500/10',
                        'icon'  => 'M16 8v8m-8-5v5m-4 4V4m4 0h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H8',
                    ],
                ];
            @endphp

            @foreach ($statistik as $kartu)
                <div class="card card-hover group relative overflow-hidden p-5">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full {{ $kartu['glow'] }} blur-2xl transition-all group-hover:scale-125"></div>
                    <div class="relative z-10 flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $kartu['warna'] }} text-white shadow-lg">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kartu['icon'] }}"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ $kartu['label'] }}</p>
                            <p class="mt-1 truncate text-2xl font-bold tracking-tight text-slate-900">{{ $kartu['nilai'] }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $kartu['sub'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================= PERLU DIPERIKSA ================= --}}
        @if ($perluPerhatian->isNotEmpty())
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
                            <h2 class="card-title">Perlu Diperiksa</h2>
                            <p class="card-sub">
                                Biaya di atas {{ $rupiah($ambangPerhatian) }} (1,5x rata-rata periode ini).
                            </p>
                        </div>
                    </div>
                    <span class="pill pill-rose">{{ $perluPerhatian->count() }} Catatan</span>
                </div>

                <ul class="divide-y divide-slate-100/70">
                    @foreach ($perluPerhatian as $item)
                        <li class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-rose-50/40 sm:px-5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01"/>
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-slate-800">{{ $item->no_plat }}</p>
                                <p class="truncate text-[11px] text-slate-500">
                                    {{ $item->kode_warna_unit }} · {{ $item->rincian_bahan }}
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-rose-600">{{ $rupiah($item->harga_nota) }}</p>
                                <p class="text-[11px] text-slate-500">{{ $item->tanggal?->format('d/m/Y') }}</p>
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
                            <p class="card-sub">Jumlah nota &amp; nilainya per cabang pada periode ini.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                    @foreach ($perCabang as $c)
                        @php $persen = $totalNilaiNota > 0 ? round(($c['nilai'] / $totalNilaiNota) * 100) : 0; @endphp
                        <div class="group relative overflow-hidden rounded-2xl border-slate-200/70 bg-gradient-to-br from-white to-slate-50/80 p-4 transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-lg">
                            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-emerald-500/5 blur-2xl transition group-hover:bg-emerald-500/10"></div>
                            <div class="relative z-10">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-slate-800">{{ $c['cabang'] }}</p>
                                        <p class="mt-0.5 text-[11px] text-slate-500">{{ number_format($c['nota']) }} nota</p>
                                    </div>
                                    <span class="pill pill-emerald shrink-0">{{ $persen }}%</span>
                                </div>

                                <p class="mt-3 text-xl font-bold tracking-tight text-slate-900">
                                    Rp {{ number_format($c['nilai'], 0, ',', '.') }}
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
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-800 text-white shadow-md shadow-slate-800/20">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <form method="GET" action="{{ route('manajer.laporan-oplosan') }}"
                  class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-[minmax(0,180px)_130px_minmax(0,240px)_auto_auto] lg:items-end">
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
                            @foreach (\App\Support\Cabang::daftar() as $c)
                                <option value="{{ $c }}" {{ $cabang === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark h-[46px]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"/>
                    </svg>
                    Terapkan
                </button>

                @if ($bulan || $tahun || $cabang)
                    <a href="{{ route('manajer.laporan-oplosan') }}" class="btn btn-ghost h-[46px]">Reset</a>
                @endif
            </form>
        </div>

        {{-- ================= DAFTAR LAPORAN ================= --}}
        <div class="card overflow-hidden">
            <div class="card-head flex">
                <div>
                    <h2 class="card-title">Daftar Nota &amp; Item Oplosan</h2>
                    <p class="card-sub">Klik satu nota untuk membuka rincian bahannya. Mode pantau - data tidak diubah dari sini.</p>
                </div>
                <span class="pill pill-emerald">{{ number_format($notas->total()) }} Nota</span>
            </div>

            @if ($notas->isEmpty())
                <div class="px-6 py-14">
                    <div class="mx-auto flex max-w-sm flex-col items-center text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 shadow-inner">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Belum ada laporan</h3>
                        <p class="mt-1 text-sm text-slate-500">Tidak ada data untuk periode yang dipilih.</p>
                    </div>
                </div>
            @else
                {{-- Satu nota = satu kartu berisi rincian bahan. Mode pantau,
                     jadi tidak ada tombol ubah data. --}}
                <div class="divide-y divide-slate-100">
                    @foreach ($notas as $nota)
                        @include('laporan_oplosan._nota', [
                            'nota' => $nota,
                            'jumlahItem' => $nota['jumlahItem'],
                            'bisaAksi' => false,
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
