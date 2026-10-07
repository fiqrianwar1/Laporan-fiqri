@extends('layouts.manajer')

@section('title', 'Riwayat Order - Mode Pantau')

@section('content')
    @php
        use App\Support\Rupiah;

        // Nilai tidak dibulatkan - sen aslinya tetap ditulis apa adanya.
        $rupiah = fn ($angka) => Rupiah::format($angka);
    @endphp

    <div class="space-y-5">
        {{-- ================= HEADER ================= --}}
        <div class="page-head">
            <div>
                <div class="pill pill-emerald mb-2">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-600"></span>
                    Mode Pantau
                </div>
                <h1 class="page-title">Riwayat Order (Pantau)</h1>
                <p class="page-sub">Periksa pemakaian anggaran belanja bahan &amp; consumable. Tanpa aksi ubah data.</p>
            </div>

            <div class="page-actions">
                <a href="{{ route('riwayat-order.pdf', request()->query()) }}" class="btn btn-outline btn-sm">
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

        {{-- ================= RINGKASAN ================= --}}
        <div class="grid gap-4 lg:grid-cols-3">
            {{-- Total belanja jadi sorotan --}}
            <div class="card relative flex h-full flex-col overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-800 to-slate-900 p-6 text-white shadow-lg shadow-indigo-900/25 lg:col-span-2">
                <div class="animate-floaty pointer-events-none absolute -right-10 -top-14 h-44 w-44 rounded-full bg-white/10 blur-3xl"></div>
                <div class="pointer-events-none absolute -left-10 bottom-0 h-32 w-32 rounded-full bg-violet-400/20 blur-3xl"></div>

                <div class="relative z-10 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-200">Total Belanja Periode Ini</p>
                        <p class="angka mt-2 text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $rupiah($totalBelanja) }}</p>
                        <p class="mt-1.5 text-xs text-indigo-100/85">
                            {{ number_format($jumlahNota) }} nota · {{ $jumlahBaris }} item · {{ $jumlahBarang }} jenis barang · sudah setelah diskon
                        </p>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/25">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v-2"/>
                        </svg>
                    </div>
                </div>

                <div class="relative z-10 mt-auto flex-wrap items-center gap-2 border-t border-white/20 pt-4">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1.5 text-[11px] font-semibold ring-1 ring-white/25">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H3a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/>
                        </svg>
                        Periode: {{ $bulan ? $namaBulan[$bulan] ?? 'Semua bulan' : 'Semua bulan' }} {{ $tahun ?: 'semua tahun' }}
                    </span>
                    @if ($bulan || $tahun)
                        <a href="{{ route('manajer.riwayat-order') }}"
                           class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-[11px] font-bold text-indigo-700 transition hover:bg-indigo-50">
                            Tampilkan semua periode
                        </a>
                    @endif
                </div>
            </div>

            {{-- Rata-rata + total diskon --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-1">
                <div class="card card-hover flex items-center gap-4 p-5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-8-5v5m-4 4V4m4 0h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H8"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Rata-rata per Nota</p>
                        <p class="angka truncate text-lg font-extrabold tracking-tight text-slate-900">{{ $rupiah($rataPerNota) }}</p>
                        <p class="text-xs text-slate-400">Nilai belanja satu nota</p>
                    </div>
                </div>

                <div class="card card-hover flex items-center gap-4 p-5">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-lg shadow-emerald-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 14 4 9l5-5M4 9h11a5 5 0 0 1 0 10h-3"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Potongan Diskon</p>
                        <p class="angka truncate text-lg font-extrabold tracking-tight text-emerald-600">{{ $rupiah($totalDiskon) }}</p>
                        <p class="text-xs text-slate-400">Hemat dari diskon pembelian</p>
                    </div>
                </div>
            </div>
        </div>

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
                            <p class="card-sub">Jumlah nota &amp; nilai belanja per cabang pada periode ini.</p>
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

                                <p class="angka mt-3 text-xl font-extrabold tracking-tight text-slate-900">
                                    {{ Rupiah::format($c['nilai']) }}
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
            <form method="GET" action="{{ route('manajer.riwayat-order') }}" class="filter-baris items-end">
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

                <div class="filter-aksi flex items-center gap-2">
                    <button type="submit" class="btn btn-dark h-[42px] flex-1 min-w-0">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"/>
                        </svg>
                        <span class="truncate">Terapkan</span>
                    </button>

                    @if ($bulan || $tahun || $cabang)
                        <a href="{{ route('manajer.riwayat-order') }}" class="btn btn-ghost h-[42px] shrink-0">Reset</a>
                    @endif
                </div>
            </form>
            </div>
        </div>

        {{-- ================= BARANG TERMAHAL ================= --}}
        @if ($barangTermahal->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="card-head flex">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-orange-500 text-white shadow-md shadow-rose-500/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v-2"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="card-title">5 Pembelian Terbesar</h2>
                            <p class="card-sub">Baris order dengan nilai paling tinggi pada periode ini.</p>
                        </div>
                    </div>
                </div>

                <ul class="divide-y divide-slate-100/70">
                    @foreach ($barangTermahal as $index => $order)
                        <li class="flex items-center gap-3 px-4 py-3.5 transition-colors hover:bg-rose-50/30 sm:px-5">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-xs font-bold {{ $index === 0 ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                                {{ $index + 1 }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $order->nama_barang }}</p>
                                <p class="truncate text-[11px] text-slate-500">
                                    {{ $order->no_bukti_faktur }} · {{ $order->qty }} {{ $order->satuan }}
                                    @if ($order->diskon_persen > 0)
                                        · diskon {{ number_format($order->diskon_persen, 2, ',', '.') }}%
                                    @endif
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-rose-600">{{ $rupiah($order->total_item) }}</p>
                                <p class="text-[11px] text-slate-500">{{ $order->tanggal?->format('d/m/Y') }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ================= DAFTAR ORDER ================= --}}
        <div class="card overflow-hidden">
            <div class="card-head flex">
                <div>
                    <h2 class="card-title">Daftar Nota &amp; Item</h2>
                    <p class="card-sub">Klik satu nota untuk membuka item barangnya. Mode pantau - data tidak diubah dari sini.</p>
                </div>
                <span class="pill pill-emerald">{{ number_format($notas->total()) }} Nota</span>
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
                        <p class="mt-1 text-sm text-slate-500">Tidak ada data untuk periode yang dipilih.</p>
                    </div>
                </div>
            @else
                {{-- Satu nota = satu kartu; itemnya dibuka-tutup. Mode pantau,
                     jadi tidak ada tombol ubah data di dalamnya. --}}
                <div class="divide-y divide-slate-100">
                    @foreach ($notas as $nota)
                        @include('riwayat_order._nota', [
                            'nota' => $nota,
                            'jumlahItem' => $nota['jumlahItem'],
                            'bisaAksi' => false,
                            'labelNama' => 'Nama Barang',
                            'labelHarga' => 'Harga',
                            'labelQty' => 'qty',
                            'kolomDiskon' => true,
                            'kolomNominalDiskon' => true,
                        ])
                    @endforeach
                </div>
            @endif
            <x-pager :paginator="$notas" anchor="daftar-order-pantau" />
        </div>
    </div>
@endsection
