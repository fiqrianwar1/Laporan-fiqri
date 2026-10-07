@extends('layouts.app')

@section('title', 'Riwayat Order')

@php
    use App\Support\Cabang;
    use App\Support\Rupiah;
@endphp
@section('content')
    <div class="space-y-5">
        {{-- ================= HEADER HALAMAN ================= --}}
        <div class="page-head">
            <div>
                <div class="pill pill-indigo mb-2">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-indigo-600"></span>
                    Manajemen Pembelian
                </div>
                <h1 class="page-title">Riwayat Order Barang</h1>
                <p class="page-sub">Rekap belanja bahan &amp; consumable, dipisah per cabang dengan total nilai tiap nota.</p>
            </div>

            <div class="page-actions">
                <a href="{{ route('riwayat-order.preview', request()->query()) }}" class="btn btn-outline btn-sm">
                    <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a2 2 0 0 1 1.414.586l5.414 5.414A2 2 0 0 1 19 10.414V19a2 2 0 0 1-2 2Z"/>
                    </svg>
                    Preview PDF
                </a>
                <a href="{{ route('riwayat-order.pdf', request()->query()) }}" class="btn btn-outline btn-sm">
                    <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0 4-4m-4 4-4-4M4 20h16"/>
                    </svg>
                    Download
                </a>
                <a href="{{ route('riwayat-order.create') }}" class="btn btn-primary btn-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Order
                </a>
            </div>
        </div>

    {{-- ================= KARTU STATISTIK ================= --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $kartu = [
                [
                    'label' => 'Total Nota',
                    'nilai' => number_format($totalNota),
                    'sub'   => number_format($totalItem) . ' item barang tercatat',
                    'warna' => 'from-blue-500 to-indigo-600',
                    'glow'  => 'bg-blue-500/10 group-hover:bg-blue-500/20',
                    'icon'  => 'M9 3v2m6-2v2M4 8h16M6 6h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z',
                ],
                [
                    'label' => 'Total Nilai Nota',
                    'nilai' => Rupiah::format($totalNilaiNota),
                    'sub'   => 'Sudah dihitung setelah diskon',
                    'warna' => 'from-emerald-400 to-teal-600',
                    'glow'  => 'bg-emerald-500/10 group-hover:bg-emerald-500/20',
                    'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v-2',
                ],
                [
                    'label' => 'Rata-rata per Nota',
                    'nilai' => Rupiah::format($rataPerNota),
                    'sub'   => 'Nilai rata-rata satu nota',
                    'warna' => 'from-amber-400 to-orange-500',
                    'glow'  => 'bg-amber-500/10 group-hover:bg-amber-500/20',
                    'icon'  => 'M3 12h4l3 8 4-16 3 8h4',
                ],
                [
                    'label' => 'Cabang Tercatat',
                    'nilai' => number_format($perCabang->count()),
                    'sub'   => $cabang ? 'Sedang difilter' : 'Semua cabang',
                    'warna' => 'from-violet-500 to-fuchsia-600',
                    'glow'  => 'bg-violet-500/10 group-hover:bg-violet-500/20',
                    'icon'  => 'M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6',
                ],
            ];
        @endphp
        @foreach ($kartu as $k)
            {{-- Ukuran kartu diatur .stat-card (lihat app.css). --}}
            <div class="stat-card">
                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full {{ $k['glow'] }} blur-2xl transition-all"></div>
                <div class="relative z-10 flex items-start gap-3.5">
                    <div class="stat-icon bg-gradient-to-br {{ $k['warna'] }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $k['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="stat-label">{{ $k['label'] }}</p>
                        <p class="angka-kartu mt-1">{{ $k['nilai'] }}</p>
                        <p class="mt-0.5 truncate text-xs text-slate-500">{{ $k['sub'] }}</p>
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
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-md shadow-indigo-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="card-title">Rincian per Cabang</h2>
                        <p class="card-sub">Total nota &amp; nilai belanja tiap cabang pada periode ini.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5">
                @foreach ($perCabang as $c)
                    @php $persen = $totalNilaiNota > 0 ? round(($c['nilai'] / $totalNilaiNota) * 100) : 0; @endphp
                    <div class="group relative overflow-hidden rounded-2xl border-slate-200/70 bg-gradient-to-br from-white to-slate-50/80 p-4 transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-lg">
                        <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-blue-500/5 blur-2xl transition group-hover:bg-blue-500/10"></div>
                        <div class="relative z-10">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-slate-800">{{ $c['cabang'] }}</p>
                                    <p class="mt-0.5 text-[11px] text-slate-500">
                                        {{ number_format($c['nota']) }} nota &middot; {{ number_format($c['item']) }} item
                                    </p>
                                </div>
                                <span class="pill pill-blue shrink-0">{{ $persen }}%</span>
                            </div>

                            <p class="angka mt-3 text-xl font-extrabold tracking-tight text-slate-900">
                                {{ Rupiah::format($c['nilai']) }}
                            </p>

                            <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-500"
                                     style="width: {{ max($persen, 2) }}%"></div>
                            </div>
                        </div>
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
                    {{ number_format($notas->total()) }} nota
                    @if ($cabang)
                        &middot; {{ $cabang }}
                    @endif
                </p>
            </div>
        </div>

        <div class="filter-panel-body">
            <form method="GET" action="{{ route('riwayat-order.index') }}" class="filter-baris items-end">
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
                    <a href="{{ route('riwayat-order.index') }}" class="btn btn-ghost h-[42px] shrink-0">Reset</a>
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
                    Klik satu nota untuk membuka daftar barangnya. Nilai di kanan adalah
                    <span class="font-semibold text-slate-600">total satu nota</span>, bukan per item.
                </p>
            </div>
            <span class="pill pill-slate">
                {{ number_format($notas->total()) }} Nota
                @if ($notas->lastPage() > 1)
                    &middot; hal. {{ $notas->currentPage() }}/{{ $notas->lastPage() }}
                @endif
            </span>
        </div>

        @if ($notas->isEmpty())
            <div class="px-6 py-14">
                <div class="mx-auto flex max-w-sm flex-col items-center text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 shadow-inner">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 14h6m-7-4h8M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Belum ada riwayat order</h3>
                    <p class="mt-1 text-sm text-slate-500">Belum ada data untuk filter yang dipilih.</p>
                    <a href="{{ route('riwayat-order.create') }}" class="btn btn-primary mt-5">

                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Order Pertama
                    </a>
                </div>
            </div>
        @else
            {{-- Satu nota = satu kartu yang bisa dibuka-tutup. Item di dalamnya
                 ditampilkan lengkap dengan tombol Edit / Hapus per item. --}}
            <div class="divide-y divide-slate-100">
                @foreach ($notas as $nota)
                    @include('riwayat_order._nota', [
                        'nota' => $nota,
                        'jumlahItem' => $nota['jumlahItem'],
                        'bisaAksi' => true,
                        'labelNama' => 'Nama Barang',
                        'labelHarga' => 'Harga',
                        'labelQty' => 'qty',
                        'kolomDiskon' => true,
                        'kolomNominalDiskon' => true,
                    ])
                @endforeach
            </div>

        @endif
        <x-pager :paginator="$notas" anchor="daftar-order" />
    </div>
</div>
@endsection
