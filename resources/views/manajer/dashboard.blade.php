@extends('layouts.manajer')

@section('title', 'Dashboard Pengawasan')

@section('content')
    @php
        $rupiah = fn ($angka) => 'Rp ' . number_format((float) $angka, 0, ',', '.');
        $maksTrenBelanja = collect($tren)->max('belanja') ?: 1;
        $maksTrenOplosan = collect($tren)->max('oplosan') ?: 1;
    @endphp

    <div class="space-y-5">
        {{-- ================= HEADER PENGUSAHA ================= --}}
        <div class="page-head">
            <div>
                <div class="pill pill-emerald mb-2">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-600"></span>
                    Mode Pengawasan
                </div>
                <h1 class="page-title">Dashboard Manajer</h1>
                <p class="page-sub">Ringkasan biaya oplosan &amp; belanja bahan untuk pengambilan keputusan.</p>
            </div>

            <div class="page-actions">
                <a href="{{ route('manajer.laporan-harian', request()->query()) }}" class="btn btn-outline btn-sm">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Laporan Harian
                </a>
                <a href="{{ route('manajer.surat-jalan', request()->query()) }}" class="btn btn-outline btn-sm">
                    <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Surat Jalan
                </a>
                <a href="{{ route('manajer.riwayat-order', request()->query()) }}" class="btn btn-outline btn-sm">
                    <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Riwayat Order
                </a>
                <a href="{{ route('manajer.laporan-oplosan', request()->query()) }}" class="btn btn-outline btn-sm">
                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 3v2m6-2v2M4 8h16M6 6h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                    </svg>
                    Laporan Oplosan
                </a>
            </div>
        </div>

        {{-- ================= KPI UTAMA (4 KARTU) ================= --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $kpi = [
                    [
                        'label' => 'Total Pengeluaran',
                        'nilai' => $rupiah($totalPengeluaran),
                        'sub'   => 'Belanja + biaya oplosan periode ini',
                        'warna' => 'from-slate-700 to-slate-900',
                        'glow'  => 'bg-slate-500/10',
                        'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v-2',
                    ],
                    [
                        'label' => 'Belanja Bahan',
                        'nilai' => $rupiah($totalBelanja),
                        'sub'   => $totalOrder . ' baris pembelian',
                        'warna' => 'from-indigo-500 to-violet-600',
                        'glow'  => 'bg-indigo-500/10',
                        'icon'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    ],
                    [
                        'label' => 'Biaya Oplosan',
                        'nilai' => $rupiah($totalBiayaOplosan),
                        'sub'   => $totalOplosan . ' laporan · rata-rata ' . $rupiah($rataOplosan),
                        'warna' => 'from-blue-500 to-blue-600',
                        'glow'  => 'bg-blue-500/10',
                        'icon'  => 'M9 17V7m6 10V7M4 4h16v16H4z',
                    ],
                    [
                        // Harga per CC = angka paling berguna untuk menilai kewajaran tinting.
                        'label' => 'Biaya per CC',
                        'nilai' => $rupiah($biayaPerCc),
                        'sub'   => number_format($totalCc) . ' CC total pemakaian',
                        'warna' => 'from-amber-400 to-orange-500',
                        'glow'  => 'bg-amber-500/10',
                        'icon'  => 'M5 13l4 4L19 7',
                    ],
                ];
            @endphp

            @foreach ($kpi as $kartu)
                {{-- Ukuran kartu diatur .stat-card (lihat app.css). --}}
                <div class="stat-card">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full {{ $kartu['glow'] }} blur-2xl transition-all group-hover:scale-125"></div>
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

        {{-- ================= KPI LAPORAN HARIAN & SURAT JALAN ================= --}}
        {{-- Melengkapi KPI biaya di atas: laporan harian menyorot MUTU KERJA
             (persentase matching warna & lama pengerjaan), surat jalan
             menyorot arus barang keluar gudang. --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $kpiOperasional = [
                    [
                        'label' => 'Laporan Harian Oplosan',
                        'nilai' => number_format($totalHarian),
                        'sub'   => number_format($totalVolumeHarian) . ' cc total volume',
                        'warna' => 'from-emerald-500 to-teal-600',
                        'glow'  => 'bg-emerald-500/10',
                        'icon'  => 'M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    ],
                    [
                        'label' => 'Matching Warna Sama',
                        'nilai' => $persenMatchingSama . '%',
                        'sub'   => number_format($jumlahMatchingSama) . ' dari ' . number_format($totalHarian) . ' pekerjaan'
                            . ($rataDurasiHarian > 0 ? ' · rata-rata ' . $rataDurasiHarian . ' menit' : ''),
                        'warna' => 'from-violet-500 to-fuchsia-600',
                        'glow'  => 'bg-violet-500/10',
                        'icon'  => 'M5 13l4 4L19 7',
                    ],
                    [
                        'label' => 'Total Surat Jalan',
                        'nilai' => number_format($totalSurat),
                        'sub'   => number_format($totalBarisSurat) . ' baris barang keluar',
                        'warna' => 'from-rose-500 to-orange-500',
                        'glow'  => 'bg-rose-500/10',
                        'icon'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    ],
                    [
                        'label' => 'Barang Keluar Gudang',
                        'nilai' => number_format($totalBarangKeluar),
                        'sub'   => 'Rata-rata ' . number_format($rataSurat, 1) . ' barang / surat',
                        'warna' => 'from-sky-500 to-blue-600',
                        'glow'  => 'bg-sky-500/10',
                        'icon'  => 'M5 17a2 2 0 104 0 2 2 0 00-4 0Zm10 0a2 2 0 104 0 2 2 0 00-4 0ZM3 6h2l2.4 10.2A2 2 0 009.35 17.6h8.3a2 2 0 001.95-1.6L21 9H6',
                    ],
                ];
            @endphp

            @foreach ($kpiOperasional as $kartu)
                <div class="stat-card">
                    <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full {{ $kartu['glow'] }} blur-2xl transition-all group-hover:scale-125"></div>
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
                        <p class="card-sub">Semua angka di halaman ini mengikuti periode yang dipilih.</p>
                    </div>
                </div>
                <span class="pill pill-slate">{{ $totalOplosan + $totalOrder }} Catatan</span>
            </div>

            <div class="filter-panel-body">
            <form method="GET" action="{{ route('manajer.dashboard') }}"
                  class="filter-baris items-end">
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

                {{-- Tombol aksi dibuat seperti halaman lain supaya
                     posisinya sejajar dengan kolom isian. --}}
                <div class="filter-aksi flex items-center gap-2">
                    <button type="submit" class="btn btn-dark h-[42px] flex-1 min-w-0">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"/>
                        </svg>
                        <span class="truncate">Terapkan</span>
                    </button>

                    @if ($bulan || $tahun)
                        <a href="{{ route('manajer.dashboard') }}" class="btn btn-ghost h-[42px] shrink-0">Reset</a>
                    @endif
                </div>
            </form>
            </div>
        </div>

        {{-- ================= PERLU DIPERIKSA ================= --}}
        {{-- Bagian paling penting untuk manajer: catatan yang biayanya
             mencolok dibanding rata-rata periode ini. --}}
        <div class="grid gap-4 xl:grid-cols-3">
            <div class="card overflow-hidden xl:col-span-2">
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
                                Biaya oplosan di atas {{ $rupiah($ambangPerhatian) }}
                                (1,5x rata-rata periode ini).
                            </p>
                        </div>
                    </div>
                    @if ($perluPerhatian->isNotEmpty())
                        <span class="pill pill-rose">{{ $perluPerhatian->count() }} Catatan</span>
                    @endif
                </div>

                @if ($perluPerhatian->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Semua biaya oplosan masih wajar</p>
                        <p class="mt-1 text-xs text-slate-500">Tidak ada catatan yang mencolok di periode ini.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($perluPerhatian as $item)
                            <li class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-rose-50/40 sm:px-5">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01"/>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-slate-800">{{ App\Support\Tanggal::panjang($item->tanggal) }}</p>
                                    <p class="truncate text-[11px] text-slate-500">
                                        {{ $item->kode_warna_unit }} · {{ $item->no_plat }} · {{ $item->rincian_bahan }}
                                    </p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-bold text-rose-600">{{ $rupiah($item->harga_nota) }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $item->cabang_area }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Ringkasan bulan berjalan + perbandingan --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Bulan Ini</h2>
                            <p class="card-sub">{{ now()->translatedFormat('F Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-2.5 p-4 sm:p-5">
                    <div class="rounded-xl border-slate-200/70 bg-white/70 px-3.5 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-medium text-slate-600">Belanja bulan ini</span>
                            <span class="text-sm font-bold text-emerald-600">{{ $rupiah($ringkasBulanIni['belanja']) }}</span>
                        </div>

                        {{-- Perbandingan dengan bulan sebelumnya --}}
                        @if ($ringkasBulanIni['perubahan'] === null)
                            <p class="mt-1 text-[11px] text-slate-400">Belum ada data bulan lalu sebagai pembanding.</p>
                        @else
                            @php $naik = $ringkasBulanIni['perubahan'] >= 0; @endphp
                            <p class="mt-1 inline-flex items-center gap-1 text-[11px] font-semibold {{ $naik ? 'text-rose-600' : 'text-emerald-600' }}">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if ($naik)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    @endif
                                </svg>
                                {{ number_format(abs($ringkasBulanIni['perubahan']), 1) }}%
                                {{ $naik ? 'lebih tinggi' : 'lebih rendah' }} dari bulan lalu
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center justify-between gap-3 rounded-xl border-slate-200/70 bg-white/70 px-3.5 py-3">
                        <span class="text-sm font-medium text-slate-600">Item order masuk</span>
                        <span class="text-sm font-bold text-indigo-600">{{ number_format($ringkasBulanIni['order']) }} item</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-xl border-slate-200/70 bg-white/70 px-3.5 py-3">
                        <span class="text-sm font-medium text-slate-600">Laporan oplosan</span>
                        <span class="text-sm font-bold text-blue-600">{{ number_format($ringkasBulanIni['oplosan']) }} laporan</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-xl border-slate-200/70 bg-white/70 px-3.5 py-3">
                        <span class="text-sm font-medium text-slate-600">Total qty oplosan</span>
                        <span class="text-sm font-bold text-amber-600">{{ number_format($ringkasBulanIni['cc']) }} CC</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-xl border-slate-200/70 bg-white/70 px-3.5 py-3">
                        <span class="text-sm font-medium text-slate-600">Laporan harian oplosan</span>
                        <span class="text-sm font-bold text-emerald-600">{{ number_format($ringkasBulanIni['harian']) }} pekerjaan</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-xl border-slate-200/70 bg-white/70 px-3.5 py-3">
                        <span class="text-sm font-medium text-slate-600">Volume harian oplosan</span>
                        <span class="text-sm font-bold text-teal-600">{{ number_format($ringkasBulanIni['volume']) }} cc</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-xl border-slate-200/70 bg-white/70 px-3.5 py-3">
                        <span class="text-sm font-medium text-slate-600">Surat jalan keluar</span>
                        <span class="text-sm font-bold text-rose-600">{{ number_format($ringkasBulanIni['surat']) }} surat</span>
                    </div>

                    <p class="pt-1 text-[11px] leading-relaxed text-slate-400">
                        Angka ini selalu dihitung dari awal bulan berjalan, tidak terpengaruh filter periode di atas.
                    </p>
                </div>
            </div>
        </div>

        {{-- ================= TREN 6 BULAN ================= --}}
        <div class="card overflow-hidden">
            <div class="card-head flex">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-md shadow-blue-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V5m0 14h16M8 16V9m4 7v-4m4 4v-7"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="card-title">Tren 6 Bulan</h2>
                        <p class="card-sub">Batang biru = jumlah laporan oplosan, batang ungu = nilai belanja bahan.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-500">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span> Laporan
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-indigo-400"></span> Belanja
                    </span>
                </div>
            </div>

            <div class="p-4 sm:p-5">
                <div class="flex h-52 items-end justify-between gap-2 sm:gap-4">
                    @foreach ($tren as $baris)
                        @php
                            $tinggiOplosan = round(($baris['oplosan'] / $maksTrenOplosan) * 100);
                            $tinggiBelanja = round(($baris['belanja'] / $maksTrenBelanja) * 100);
                        @endphp
                        <div class="group flex h-full flex-1 flex-col items-center justify-end gap-2">
                            <div class="flex h-full w-full items-end justify-center gap-1 sm:gap-1.5">
                                <div class="relative flex h-full w-1/2 max-w-[26px] items-end">
                                    <div class="w-full rounded-t-lg bg-gradient-to-t from-blue-600 to-blue-400 transition-all duration-500 group-hover:from-blue-700"
                                         style="height: {{ max($tinggiOplosan, 2) }}%"
                                         title="{{ $baris['label'] }}: {{ $baris['oplosan'] }} laporan ({{ number_format($baris['cc']) }} CC)"></div>
                                </div>
                                <div class="relative flex h-full w-1/2 max-w-[26px] items-end">
                                    <div class="w-full rounded-t-lg bg-gradient-to-t from-indigo-500 to-indigo-300 transition-all duration-500"
                                         style="height: {{ max($tinggiBelanja, 2) }}%"
                                         title="{{ $baris['label'] }}: belanja {{ $rupiah($baris['belanja']) }}"></div>
                                </div>
                            </div>
                            <p class="text-[11px] font-semibold text-slate-500">{{ $baris['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ================= UNIT & BARANG TERATAS ================= --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            {{-- Unit dengan pemakaian terbanyak --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 17a2 2 0 1 0 4 0 2 2 0 0 0-4 0Zm10 0a2 2 0 1 0 4 0 2 2 0 0 0-4 0ZM3 6h2l2.4 10.2A2 2 0 0 0 9.35 17.6h8.3a2 2 0 0 0 1.95-1.6L21 9H6"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Unit Pemakai Terbanyak</h2>
                            <p class="card-sub">Diurutkan dari total CC pada periode ini.</p>
                        </div>
                    </div>
                </div>

                @if ($topUnit->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada data unit</p>
                        <p class="mt-1 text-xs text-slate-500">Data muncul setelah laporan oplosan dicatat.</p>
                    </div>
                @else
                    @php $maksUnit = $topUnit->max('cc') ?: 1; @endphp
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($topUnit as $index => $unit)
                            <li class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-blue-50/40 sm:px-5">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $index + 1 }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $unit['plat'] }}</p>
                                    <p class="truncate text-[11px] text-slate-500">{{ $unit['warna'] }}</p>
                                    <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-500"
                                             style="width: {{ max(round(($unit['cc'] / $maksUnit) * 100), 4) }}%"></div>
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-bold text-slate-900">{{ number_format($unit['cc']) }} <span class="text-[11px] font-semibold text-slate-400">CC</span></p>
                                    <p class="text-[11px] text-slate-500">{{ $unit['jumlah'] }}x · {{ $rupiah($unit['biaya']) }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Barang penyedot anggaran terbesar --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-md shadow-indigo-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v-2"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Penyedot Anggaran</h2>
                            <p class="card-sub">Barang dengan nilai belanja terbesar.</p>
                        </div>
                    </div>
                </div>

                @if ($topPemasok->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada data pembelian</p>
                        <p class="mt-1 text-xs text-slate-500">Data muncul setelah order dicatat.</p>
                    </div>
                @else
                    @php $maksBelanja = $topPemasok->max('belanja') ?: 1; @endphp
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($topPemasok as $index => $barang)
                            <li class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-indigo-50/40 sm:px-5">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold {{ $index === 0 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $index + 1 }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $barang['nama'] }}</p>
                                    <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-violet-500"
                                             style="width: {{ max(round(($barang['belanja'] / $maksBelanja) * 100), 4) }}%"></div>
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="angka-sel text-sm">{{ $rupiah($barang['belanja']) }}</p>
                                    <p class="text-[11px] text-slate-500">{{ number_format($barang['qty']) }} unit</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- ================= AKTIVITAS TERBARU ================= --}}
        <div class="card overflow-hidden">
            <div class="card-head flex">
                <div>
                    <h2 class="card-title">Aktivitas Terbaru</h2>
                    <p class="card-sub">Campuran laporan oplosan & order terakhir pada periode ini.</p>
                </div>
            </div>

            @if ($aktivitas->isEmpty())
                <div class="px-6 py-10 text-center">
                    <p class="text-sm font-semibold text-slate-700">Belum ada aktivitas</p>
                    <p class="mt-1 text-xs text-slate-500">Data akan muncul setelah ada catatan baru.</p>
                </div>
            @else
                <ul class="divide-y divide-slate-100/70">
                    @foreach ($aktivitas as $item)
                        @php $oplosan = $item['jenis'] === 'oplosan'; @endphp
                        <li class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-slate-50 sm:px-5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $oplosan ? 'bg-blue-50 text-blue-600' : 'bg-indigo-50 text-indigo-600' }}">
                                @if ($oplosan)
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M4 8h16M6 6h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                                    </svg>
                                @else
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                @endif
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $item['judul'] }}</p>
                                <p class="truncate text-[11px] text-slate-500">{{ $item['rincian'] }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-slate-900">{{ $item['nilai'] }}</p>
                                <p class="text-[11px] text-slate-500">
                                    {{ $oplosan ? 'Oplosan' : 'Order' }} · {{ \App\Support\Tanggal::panjang($item['tanggal']) }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- ================= PANTAU HARIAN & SURAT JALAN ================= --}}
        {{-- Dua daftar ini melengkapi aktivitas oplosan & order di atas,
             supaya dari dashboard manajer bisa langsung dilihat: mutu kerja
             oplosan harian (yang belum matching sama) dan barang apa saja
             yang baru keluar gudang. --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            {{-- Laporan harian: matching belum sama --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Matching Belum Sama</h2>
                            <p class="card-sub">Pekerjaan harian yang warnanya belum pas - perlu ditelusuri.</p>
                        </div>
                    </div>
                    <a href="{{ route('manajer.laporan-harian', request()->query()) }}" class="text-xs font-bold text-emerald-600 transition hover:text-emerald-700">Lihat semua →</a>
                </div>

                @if ($perluMatching->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Semua matching warna sudah sama</p>
                        <p class="mt-1 text-xs text-slate-500">Tidak ada pekerjaan yang perlu ditelusuri di periode ini.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($perluMatching as $baris)
                            <li class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-emerald-50/40 sm:px-5">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01"/>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-slate-800">{{ $baris->plat_nomor }} · {{ $baris->kode_warna }}</p>
                                    <p class="truncate text-[11px] text-slate-500">
                                        {{ \App\Support\Tanggal::panjang($baris->tanggal) }} · {{ $baris->bahan_cat }} · {{ number_format($baris->volume_cc) }} cc
                                    </p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-bold text-amber-600">{{ $baris->hasil_matching }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $baris->cabang_area }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Surat jalan terbaru --}}
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-orange-500 text-white shadow-md shadow-rose-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">Barang Keluar Terbaru</h2>
                            <p class="card-sub">Surat jalan terakhir - kemana barang dikirim dan berapa jumlahnya.</p>
                        </div>
                    </div>
                    <a href="{{ route('manajer.surat-jalan', request()->query()) }}" class="text-xs font-bold text-rose-600 transition hover:text-rose-700">Lihat semua →</a>
                </div>

                @if ($aktivitasSurat->isEmpty())
                    <div class="px-6 py-10 text-center">
                        <p class="text-sm font-semibold text-slate-700">Belum ada surat jalan</p>
                        <p class="mt-1 text-xs text-slate-500">Data muncul setelah barang keluar gudang dicatat.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100/70">
                        @foreach ($aktivitasSurat as $surat)
                            @php $barangPertama = $surat['items']->first(); @endphp
                            <li class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-rose-50/40 sm:px-5">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-slate-800">{{ $surat['nomor'] ?? 'Tanpa nomor' }}</p>
                                    <p class="truncate text-[11px] text-slate-500">
                                        {{ \App\Support\Tanggal::panjang($surat['tanggal']) }} · {{ $barangPertama->nama_barang }}@if ($surat['jumlahItem'] > 1), dll.@endif
                                    </p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-bold text-slate-900">{{ number_format($surat['total']) }} <span class="text-[11px] font-semibold text-slate-400">barang</span></p>
                                    <p class="text-[11px] text-slate-500">{{ $barangPertama->cabang_area }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <p class="pb-2 text-center text-[11px] text-slate-400">
            Halaman pengawasan - data berasal dari catatan yang diisi Tinter.
        </p>
    </div>
@endsection
