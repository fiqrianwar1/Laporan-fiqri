<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laporan') · Warna Tanjung Jaya</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #f6f8fb 0%, #e5ebf4 100%);
            min-height: 100vh;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
@php
    $bolehIsi = auth()->check() && auth()->user()->role === 'tinter';

    // Halaman manajer menyetel $menuUtama sendiri (menu pengawasan).
    // Kalau sudah diisi, jangan ditimpa dengan menu tinter.
    $menuUtama = $menuUtama ?? [
        [
            'label' => 'Dashboard',
            'desc'  => 'Ringkasan & statistik',
            'route' => 'dashboard',
            'aktif' => request()->routeIs('dashboard'),
            'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        ],
        [
            'label' => 'Laporan Harian',
            'desc'  => 'Catatan oplosan per unit',
            'route' => 'laporan-harian.index',
            'aktif' => request()->routeIs('laporan-harian.*'),
            'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        ],
        [
            'label' => 'Laporan Oplosan',
            'desc'  => 'Rekap tinting per nota',
            'route' => 'laporan-oplosan.index',
            'aktif' => request()->routeIs('laporan-oplosan.*'),
            'icon'  => 'M9 3v2m6-2v2M4 8h16M6 6h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z',
        ],
        [
            'label' => 'Riwayat Order',
            'desc'  => 'Belanja bahan & consumable',
            'route' => 'riwayat-order.index',
            'aktif' => request()->routeIs('riwayat-order.*'),
            'icon'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        ],
        [
            'label' => 'Surat Jalan',
            'desc'  => 'Barang keluar gudang',
            'route' => 'surat-jalan.index',
            'aktif' => request()->routeIs('surat-jalan.*'),
            'icon'  => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ],
    ];

    // Ditampilkan maks 2 kolom biar tidak berdesakan saat sidebar disembunyikan.
    // Label dibuat jelas per tombol (bukan cuma "Cetak" dua kali) supaya
    // pengguna tahu bedanya sebelum mengklik.
    $aksiCepat = [
        ['short' => 'Catat Harian',    'label' => 'Catat Laporan Harian',  'route' => 'laporan-harian.create',  'accent' => 'emerald', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        ['short' => 'Catat Oplosan',   'label' => 'Catat Laporan Oplosan', 'route' => 'laporan-oplosan.create', 'accent' => 'blue',    'icon' => 'M9 3v2m6-2v2M4 8h16M6 6h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z'],
        ['short' => 'Catat Order',     'label' => 'Catat Pembelian Bahan', 'route' => 'riwayat-order.create',   'accent' => 'indigo',  'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['short' => 'Catat Surat',     'label' => 'Catat Surat Jalan',     'route' => 'surat-jalan.create',     'accent' => 'rose',    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['short' => 'PDF Harian',      'label' => 'Preview PDF Harian',    'route' => 'laporan-harian.preview', 'accent' => 'amber',   'icon' => 'M9 12h6m-6 4h6M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z'],
        ['short' => 'PDF Oplosan',     'label' => 'Preview PDF Oplosan',   'route' => 'laporan-oplosan.preview', 'accent' => 'sky',    'icon' => 'M9 12h6m-6 4h6M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z'],
        ['short' => 'PDF Order',       'label' => 'Preview PDF Order',     'route' => 'riwayat-order.preview',   'accent' => 'violet',  'icon' => 'M9 12h6m-6 4h6M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z'],
        ['short' => 'PDF Surat',       'label' => 'Preview PDF Surat Jalan', 'route' => 'surat-jalan.preview',   'accent' => 'rose',    'icon' => 'M9 12h6m-6 4h6M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z'],
    ];
@endphp
<body class="app-bg text-slate-800">
    {{-- Indikator progres baca halaman --}}
    <div id="scroll-progress" class="fixed inset-x-0 top-0 z-[60] h-0.5 bg-gradient-to-r from-blue-500 via-indigo-500 to-violet-500 transition-[width] duration-150"></div>

    {{-- ================= SIDEBAR (desktop / layar lebar) ================= --}}
    <aside id="app-sidebar"
           class="fixed inset-y-0 left-0 z-50 hidden w-72 flex-col border-r border-white/60 glass-panel lg:flex">

        {{-- Brand + tombol buka/tutup sidebar.
             tombol TIDAK dibungkus .side-full supaya tetap kelihatan
             saat sidebar dikecilkan (kalau tersembunyi, tidak ada cara
             membesarkannya lagi). --}}
        <div class="sidebar-head flex shrink-0 items-center gap-3 px-4 py-4">
            @php $logoSidebar = \App\Support\Logo::ada() ? asset('images/' . basename(\App\Support\Logo::path())) : null; @endphp
            <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-1 shadow-lg shadow-blue-500/30 ring-1 ring-blue-100"
                 title="Warna Tanjung Jaya">
                @if ($logoSidebar)
                    <img src="{{ $logoSidebar }}" alt="Logo WTJ" class="h-full w-full object-contain">
                @else
                    <span class="text-lg font-bold text-blue-700">W</span>
                @endif
            </div>
            <div class="side-full min-w-0 flex-1">
                <p class="truncate text-sm font-bold leading-tight tracking-tight text-slate-900">Warna Tanjung Jaya</p>
                <p class="truncate text-xs font-medium text-slate-500">Laporan Fiqri</p>
            </div>
            <button type="button" id="sidebar-toggle"
                    class="js-only inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border-slate-200/70 bg-white/70 text-slate-500 transition hover:bg-white hover:text-slate-800"
                    data-sidebar-toggle
                    title="Tutup / buka sidebar" aria-label="Tutup atau buka sidebar">
                <svg class="icon-collapse h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <svg class="icon-expand h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Tombol buka (mode kecil/rail). Hanya muncul saat sidebar dikecilkan. --}}
        <button type="button" data-sidebar-toggle id="sidebar-expand"
                class="js-only hidden shrink-0 items-center justify-center rounded-xl border-slate-200/70 bg-white/70 text-slate-500 transition hover:bg-white hover:text-blue-700"
                title="Besarkan sidebar" aria-label="Besarkan sidebar">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- Tombol pencarian (Ctrl + K) --}}
        <div class="side-full shrink-0 px-4">
            <button type="button" data-palette-open
                    class="flex w-full items-center gap-2 rounded-xl border-slate-200/70 bg-white/70 px-3 py-2.5 text-sm text-slate-500 transition hover:border-slate-300 hover:bg-white">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"/>
                </svg>
                <span class="flex-1 text-left">Cari menu...</span>
                <kbd class="rounded-md border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] font-bold text-slate-500">Ctrl K</kbd>
            </button>
        </div>

        {{-- Menu utama --}}
        <nav class="mt-4 min-h-0 flex-1 space-y-1.5 overflow-y-auto overflow-x-hidden px-3">
            <p class="side-full px-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Menu</p>
            @foreach ($menuUtama as $item)
                <a href="{{ route($item['route']) }}"
                   title="{{ $item['label'] }}"
                   class="side-link flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-slate-600 hover:bg-white/80 hover:text-slate-900 {{ $item['aktif'] ? 'active' : '' }}">
                    <span class="menu-icon flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $item['aktif'] ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-slate-100 text-slate-500' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                    </span>
                    <span class="side-full min-w-0">
                        <span class="block truncate">{{ $item['label'] }}</span>
                        <span class="block truncate text-[11px] font-normal text-slate-400">{{ $item['desc'] }}</span>
                    </span>
                </a>
            @endforeach
            @if ($bolehIsi)
                <p class="side-full px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Aksi cepat</p>

                {{-- Versi penuh: 2 kolom supaya tetap rapi walau sidebar pas-pasan --}}
                <div class="side-full grid-cols-2 gap-2">
                    @foreach ($aksiCepat as $item)
                        <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
                           class="group flex-col gap-2 rounded-xl border-slate-200/70 bg-white/85 p-2.5 text-left text-[11px] font-semibold leading-tight text-slate-600 transition hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50/60 hover:text-blue-700 hover:shadow-sm">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-{{ $item['accent'] }}-50 text-{{ $item['accent'] }}-600 ring-1 ring-{{ $item['accent'] }}-100 transition group-hover:bg-{{ $item['accent'] }}-100">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                </svg>
                            </span>
                            {{ $item['short'] }}
                        </a>
                    @endforeach
                </div>

                {{-- Versi rail: ikon saja, sejajar dengan menu di atasnya --}}
                @foreach ($aksiCepat as $item)
                    <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
                       class="side-rail hidden items-center justify-center rounded-xl px-3 py-2.5 text-{{ $item['accent'] }}-600 transition hover:bg-white/80">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-{{ $item['accent'] }}-50 ring-1 ring-{{ $item['accent'] }}-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                            </svg>
                        </span>
                    </a>
                @endforeach
            @endif
        </nav>

        {{-- Kartu user + logout --}}
        @auth
            {{-- Versi penuh --}}
            <div class="side-full mt-auto shrink-0 p-3">
            <div class="side-user rounded-2xl border-white/70 bg-white/85 p-3 shadow-sm">
                <div class="relative z-10 flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-sm font-bold text-white shadow-md shadow-blue-500/30">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                        <p class="inline-flex items-center gap-1 text-[11px] font-semibold {{ $bolehIsi ? 'text-blue-600' : 'text-emerald-600' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $bolehIsi ? 'bg-blue-500' : 'bg-emerald-500' }}"></span>
                            {{ $bolehIsi ? 'Bisa isi laporan' : 'Hanya lihat laporan' }}
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="relative z-10 mt-3">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
            </div>

            {{-- Versi rail (ikon saja). Ditaruh di blok user supaya hanya
                 ada SATU elemen ber-mt-auto — kalau ada dua, keduanya
                 saling tarik dan kartu user jadi terpotong. --}}
            <div class="side-rail mt-auto hidden shrink-0 flex-col items-center gap-2 px-3 py-3">
                <button type="button" data-palette-open
                        class="flex h-9 w-9 items-center justify-center rounded-xl border-slate-200/70 bg-white/70 text-slate-500 transition hover:bg-white hover:text-blue-700"
                        title="Cari menu (Ctrl + K)" aria-label="Cari menu">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"/>
                    </svg>
                </button>
                <div class="ring-2 ring-blue-500/20 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-sm font-bold text-white shadow-md shadow-blue-500/30"
                     title="{{ auth()->user()->name }} · {{ $bolehIsi ? 'Bikin Laporan' : 'Cek Laporan' }}">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex h-9 w-9 items-center justify-center rounded-xl border-slate-200 bg-white text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                            title="Keluar" aria-label="Keluar">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        @endauth
    </aside>

    {{-- ================= TOPBAR ================= --}}
    {{-- Padding kiri topbar ditangani #app-topbar (lihat CSS .is-collapsed),
         jangan ditambah utility lain supaya tidak menumpuk. --}}
    <nav id="app-topbar" class="sticky top-0 z-40 border-b border-white/60 glass-panel lg:pl-72">
        <div class="flex h-16 items-center justify-between gap-3 px-4 sm:px-6 lg:h-18">
            {{-- Brand: di desktop disembunyikan karena sudah ada di sidebar --}}
            <div class="wtj-mobile-only flex min-w-0 items-center gap-3">
                @php $logoTopbar = \App\Support\Logo::ada() ? asset('images/' . basename(\App\Support\Logo::path())) : null; @endphp
                <div class="ring-2 ring-blue-500/20 flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl bg-white p-1 shadow-lg shadow-blue-500/30">
                    @if ($logoTopbar)
                        <img src="{{ $logoTopbar }}" alt="Logo WTJ" class="h-full w-full object-contain">
                    @else
                        <span class="text-lg font-bold text-blue-700">W</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-bold leading-tight tracking-tight text-slate-900 sm:text-base">Warna Tanjung Jaya</p>
                    <p class="truncate text-[11px] font-medium text-slate-500">{{ trim($__env->yieldContent('title', 'Laporan')) }}</p>
                </div>
            </div>

            {{-- Sisi kanan --}}
            <div class="flex items-center gap-2">
                <div class="wtj-mobile-only flex items-center gap-2">
                    {{-- Cari menu --}}
                    <button type="button" data-palette-open
                            class="js-only inline-flex h-10 w-10 items-center justify-center rounded-xl border-slate-200/70 bg-white/70 text-slate-500 transition hover:bg-white hover:text-slate-800 sm:w-auto sm:px-3"
                            aria-label="Cari menu">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"/>
                        </svg>
                        <span class="ml-2 hidden text-sm font-medium sm:inline">Cari menu</span>
                    </button>
                </div>

                @auth
                    {{-- Info user: hanya sampai tablet, karena di desktop
                         sudah ditampilkan di kartu dalam sidebar. --}}
                    <div class="wtj-mobile-only hidden items-center gap-3 rounded-xl border-white/70 bg-white/70 px-3 py-2 sm:flex">
                        <div class="text-right">
                            <p class="text-sm font-semibold leading-tight text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] font-bold uppercase tracking-wide {{ $bolehIsi ? 'text-blue-600' : 'text-emerald-600' }}">
                                {{ $bolehIsi ? 'Mode Isi' : 'Mode Lihat' }}
                            </p>
                        </div>
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-sm font-bold text-white shadow-md shadow-blue-500/30">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>

                    {{-- Info user + tombol keluar versi HP/tablet.
                         Di desktop keduanya sudah ada di sidebar, jadi
                         cukup ditampilkan sampai breakpoint lg.
                         Catatan: pengendali tampil/sembunyi ditaruh di
                         elemen pembungkus, bukan di <button>, supaya
                         tidak kalah oleh utility display di dalamnya. --}}
                    <form method="POST" action="{{ route('logout') }}" class="wtj-mobile-only block">
                        @csrf
                        <button type="submit"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border-slate-200 bg-white/70 text-slate-500 transition-all duration-300 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                                title="Keluar" aria-label="Keluar">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                @endauth
                {{-- Tombol menu (mobile / tablet) --}}
                <button type="button" data-sheet-open
                        class="js-only inline-flex h-10 w-10 items-center justify-center rounded-xl border-slate-200/70 bg-white/70 text-slate-600 transition hover:bg-white wtj-mobile-only"
                        aria-label="Buka menu">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- ================= LEMBAR MENU (tablet & HP) ================= --}}
    <div id="menu-sheet" class="js-only sheet-closed fixed inset-0 z-[70] lg:hidden">
        <div data-sheet-close class="sheet-backdrop absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

        <div class="sheet-panel absolute inset-x-0 bottom-0 max-h-[88vh] overflow-y-auto rounded-t-3xl border-t border-white/70 bg-white/95 p-4 pb-6 shadow-2xl backdrop-blur-xl">
            <div class="mx-auto mb-3 h-1.5 w-12 rounded-full bg-slate-200"></div>

            @auth
                <div class="flex items-center gap-3 rounded-2xl border-slate-200/70 bg-slate-50/80 p-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-sm font-bold text-white shadow-md shadow-blue-500/30">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] font-bold uppercase tracking-wide {{ $bolehIsi ? 'text-blue-600' : 'text-emerald-600' }}">
                            {{ $bolehIsi ? 'Mode Isi' : 'Mode Lihat' }}
                        </p>
                    </div>
                </div>
            @endauth
            <p class="mt-4 px-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Menu</p>
            <div class="mt-1 grid gap-1.5">
                @foreach ($menuUtama as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition {{ $item['aktif'] ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $item['aktif'] ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-slate-100 text-slate-500' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                            </svg>
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate">{{ $item['label'] }}</span>
                            <span class="block truncate text-[11px] font-normal text-slate-400">{{ $item['desc'] }}</span>
                        </span>
                    </a>
                @endforeach
            </div>

            @if ($bolehIsi)
                <p class="mt-4 px-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Aksi cepat</p>
                <div class="mt-1 grid-cols-2 gap-2">
                    @foreach ($aksiCepat as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex flex-col items-start gap-2 rounded-2xl border-slate-200/70 bg-white p-3 text-left text-xs font-semibold text-slate-600 transition hover:border-blue-200 hover:bg-blue-50/50 hover:text-blue-700">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                </svg>
                            </span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            @endif
            @auth
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-rose-600 transition-all duration-300 hover:border-rose-200 hover:bg-rose-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar dari akun
                    </button>
                </form>
            @endauth
            <p class="mt-4 text-center text-[11px] text-slate-400">© {{ date('Y') }} Warna Tanjung Jaya</p>
        </div>
    </div>

    {{-- ================= KONTEN ================= --}}
    <div id="app-content" class="lg:pl-72">
        {{-- pb-28 di HP: memberi ruang untuk navigasi bawah, ditambah
             safe-area pada perangkat ber-notch supaya baris terakhir
             tidak tertutup. --}}
        <main class="mx-auto w-full max-w-7xl px-4 pt-5 sm:px-6 lg:px-8 lg:pt-8">
            <div class="pb-[calc(6.5rem+env(safe-area-inset-bottom,0px))] lg:pb-12">
                @if (session('success'))
                    <div data-toast class="animate-slide-down mb-5 flex items-center gap-3 rounded-2xl border-emerald-500/20 bg-emerald-500/10 px-4 py-3.5 text-emerald-700 shadow-sm backdrop-blur-sm sm:px-5">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-500/20">
                            <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                        <button type="button" onclick="this.parentElement.remove()" class="js-only ml-auto text-emerald-600/70 transition hover:text-emerald-700" aria-label="Tutup">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif
                {{-- Banner jarak pandang: "Anda sedang di mana" + aksi cepat.
                     Hanya muncul untuk pengguna yang sudah login dan tidak
                     ditampilkan di dashboard (di sana sudah ada header sendiri). --}}
                @auth
                    @unless (request()->routeIs('dashboard') || request()->routeIs('manajer.*'))
                        @php
                            $tglAktif = \Carbon\Carbon::now()->locale('id');
                            $sudahFilter = request()->filled('bulan') || request()->filled('tahun') || request()->filled('cabang');
                        @endphp
                        {{-- Banner sapaan: menyatu dengan konten (tanpa bayangan
                             mengambang) supaya hierarkinya jelas: banner dulu,
                             baru kartu-kartu isi di bawahnya. --}}
                        <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-white/70 bg-white/80 px-4 py-3.5 backdrop-blur-xl sm:flex-row sm:items-center sm:justify-between sm:px-5">
                            <div class="flex min-w-0 flex-1 items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-sm font-bold text-white shadow-md shadow-blue-500/30">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-slate-800">
                                        Selamat bekerja, {{ auth()->user()->name }}{{ $bolehIsi ? '' : '!' }}
                                    </p>
                                    <p class="truncate text-xs text-slate-500">
                                        {{ $tglAktif->translatedFormat('l, d F Y') }}
                                        <span class="mx-1 text-slate-300">•</span>
                                        <span class="font-semibold {{ $bolehIsi ? 'text-blue-600' : 'text-emerald-600' }}">
                                            {{ $bolehIsi ? 'Mode isi: bisa tambah, ubah, hapus' : 'Mode lihat: hanya baca data' }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex w-full shrink-0 flex-wrap items-center gap-2 sm:w-auto sm:justify-end">
                                @if ($sudahFilter)
                                    <a href="{{ url()->current() }}" class="chip" title="Tampilkan semua periode">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                        Reset filter
                                    </a>
                                @endif
                                @if ($bolehIsi)
                                    @php
                                        // Tombol pintas ini menyesuaikan halaman yang
                                        // sedang dibuka, jadi pengguna langsung diarahkan
                                        // ke form tambah yang relevan.
                                        $tambahCepat = match (true) {
                                            request()->routeIs('riwayat-order*') => ['label' => 'Tambah Order', 'route' => 'riwayat-order.create'],
                                            request()->routeIs('surat-jalan*')    => ['label' => 'Tambah Surat', 'route' => 'surat-jalan.create'],
                                            default                              => ['label' => 'Tambah Laporan', 'route' => 'laporan-oplosan.create'],
                                        };
                                    @endphp
                                    <a href="{{ route($tambahCepat['route']) }}"
                                       class="btn btn-primary btn-sm">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        {{ $tambahCepat['label'] }}
                                    </a>
                                @endif
                                <button type="button" data-palette-open class="chip" title="Cari menu (Ctrl + K)">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"/>
                                    </svg>
                                    Cari menu
                                </button>
                            </div>
                        </div>
                    @endunless
                @endauth
                @yield('content')
            </div>
        </main>
    </div>

{{-- ================= NAVIGASI BAWAH (HP) ================= --}}
@auth
    {{-- Label pendek: nama menu ditulis ulang supaya muat satu baris
         ("Dashboard" jadi "Beranda", dsb). --}}
    <nav class="bottom-nav fixed inset-x-0 bottom-0 z-50 border-t border-white/70 glass-panel lg:hidden">
        <div class="mx-auto flex max-w-lg items-stretch justify-around">
            @foreach ($menuUtama as $item)
                <a href="{{ route($item['route']) }}"
                   title="{{ $item['label'] }}"
                   class="bottom-link flex flex-1 flex-col items-center gap-1 px-2 py-2.5 text-[11px] font-semibold {{ $item['aktif'] ? 'active text-blue-700' : 'text-slate-500' }}">
                    <span class="bottom-icon flex h-9 w-9 items-center justify-center rounded-xl transition-all duration-300 {{ $item['aktif'] ? '' : 'text-slate-500' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                    </span>
                    {{ ['Dashboard' => 'Beranda', 'Laporan Harian' => 'Harian', 'Laporan Oplosan' => 'Oplosan', 'Riwayat Order' => 'Order', 'Surat Jalan' => 'Surat'][$item['label']] ?? $item['label'] }}
                </a>
            @endforeach
            <button type="button" data-sheet-open
                    class="flex flex-1 flex-col items-center gap-1 px-2 py-2.5 text-[11px] font-semibold text-slate-500">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl transition-all duration-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </span>
                Menu
            </button>
        </div>
    </nav>
@endauth
{{-- ================= COMMAND PALETTE ================= --}}
<div id="command-palette" class="js-only palette-closed fixed inset-0 z-[80] items-start justify-center px-4 pt-[12vh]">
    <div data-palette-close class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div class="relative w-full max-w-lg overflow-hidden rounded-2xl border-white/70 bg-white/95 shadow-2xl backdrop-blur-xl">
        <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3">
            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z"/>
            </svg>
            <input id="palette-input" type="text" placeholder="Cari halaman atau aksi..."
                   class="w-full border-0 bg-transparent p-0 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:ring-0">
            <kbd class="rounded-md border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] font-bold text-slate-500">Esc</kbd>
        </div>
        <div id="palette-list" class="max-h-72 overflow-y-auto p-2"></div>
    </div>
</div>

@stack('scripts')

<script>
                        (function () {
                            const bolehIsi = @json($bolehIsi);

        /* ===== Sidebar buka/tutup =====
           Pilihan disimpan di localStorage, jadi sekali ditutup
           tetap tertutup walau halaman pindah atau di-refresh. */
        const sidebarKey = 'wtj-sidebar-collapsed';

        function applySidebar(collapsed) {
            document.body.classList.toggle('is-collapsed', collapsed);
        }

        // Terapkan secepat mungkin biar tidak berkedip saat halaman dimuat.
        if (window.innerWidth >= 1024) {
            try {
                applySidebar(localStorage.getItem(sidebarKey) === '1');
            } catch (e) {
                // localStorage bisa diblokir; sidebar cukup tetap terbuka.
            }
        }

        function toggleSidebar() {
            const collapsed = !document.body.classList.contains('is-collapsed');
            applySidebar(collapsed);
            try {
                localStorage.setItem(sidebarKey, collapsed ? '1' : '0');
            } catch (e) {
                // abaikan
            }
        }

        // Dua tombol: #sidebar-toggle (mode lebar) dan #sidebar-expand
        // (mode kecil). Keduanya ditangkap lewat data-sidebar-toggle
        // supaya tidak ada tombol yang lupa dipasangi handler.
        document.querySelectorAll('[data-sidebar-toggle]').forEach(btn => {
            btn.addEventListener('click', toggleSidebar);
        });

        // Pintasan: tekan "[" untuk buka/tutup sidebar (di luar kolom isian).
        document.addEventListener('keydown', function (e) {
            if (e.key !== '[') return;
            const tag = (e.target && e.target.tagName) || '';
            if (/INPUT|TEXTAREA|SELECT/.test(tag) || (e.target && e.target.isContentEditable)) return;
            if (window.innerWidth < 1024) return;
            e.preventDefault();
            toggleSidebar();
        });

                            /* Daftar menu untuk pencarian cepat */
                            const daftarMenu = [
                                { label: 'Dashboard', sub: 'Ringkasan & statistik', url: @json(route('dashboard')) },
                                { label: 'Laporan Harian', sub: 'Catatan oplosan per unit', url: @json(route('laporan-harian.index')) },
                                { label: 'Laporan Oplosan', sub: 'Lihat rekap tinting per nota', url: @json(route('laporan-oplosan.index')) },
                                { label: 'Riwayat Order', sub: 'Lihat belanja bahan & consumable', url: @json(route('riwayat-order.index')) },
                                { label: 'Surat Jalan', sub: 'Lihat barang keluar gudang', url: @json(route('surat-jalan.index')) },
                                { label: 'Preview PDF Laporan Harian', sub: 'Cek dokumen sebelum cetak', url: @json(route('laporan-harian.preview')) },
                                { label: 'Preview PDF Laporan Oplosan', sub: 'Cek dokumen sebelum cetak', url: @json(route('laporan-oplosan.preview')) },
                                { label: 'Preview PDF Riwayat Order', sub: 'Cek dokumen sebelum cetak', url: @json(route('riwayat-order.preview')) },
                                { label: 'Preview PDF Surat Jalan', sub: 'Cek dokumen sebelum cetak', url: @json(route('surat-jalan.preview')) },
                                { label: 'Download PDF Laporan Harian', sub: 'Unduh berkas langsung', url: @json(route('laporan-harian.pdf')) },
                                { label: 'Download PDF Laporan Oplosan', sub: 'Unduh berkas langsung', url: @json(route('laporan-oplosan.pdf')) },
                                { label: 'Download PDF Riwayat Order', sub: 'Unduh berkas langsung', url: @json(route('riwayat-order.pdf')) },
                                { label: 'Download PDF Surat Jalan', sub: 'Unduh berkas langsung', url: @json(route('surat-jalan.pdf')) },
                                { label: 'Keluar', sub: 'Logout dari sistem', url: @json(route('logout')), post: true },
                            ];

                            if (bolehIsi) {
                                daftarMenu.unshift(
                                    { label: 'Tambah Laporan Harian', sub: 'Catat pekerjaan oplosan baru', url: @json(route('laporan-harian.create')) },
                                    { label: 'Tambah Laporan Oplosan', sub: 'Catat oplosan baru', url: @json(route('laporan-oplosan.create')) },
                                    { label: 'Tambah Riwayat Order', sub: 'Catat pembelian baru', url: @json(route('riwayat-order.create')) },
                                    { label: 'Tambah Surat Jalan', sub: 'Catat barang keluar baru', url: @json(route('surat-jalan.create')) }
                                );
                            }

                            /* Lembar menu bawah (HP / tablet) */
                            const sheet = document.getElementById('menu-sheet');
                            const openSheet = () => sheet && sheet.classList.remove('sheet-closed');
                            const closeSheet = () => sheet && sheet.classList.add('sheet-closed');

                            document.querySelectorAll('[data-sheet-open]').forEach(el => el.addEventListener('click', openSheet));
                            document.querySelectorAll('[data-sheet-close]').forEach(el => el.addEventListener('click', closeSheet));

                            /* Command palette */
                            const palette = document.getElementById('command-palette');
                            const paletteInput = document.getElementById('palette-input');
                            const paletteList = document.getElementById('palette-list');

                            function renderPalette(query) {
                                if (!paletteList) return;
                                const q = (query || '').trim().toLowerCase();
                                const hasil = daftarMenu.filter(item =>
                                    !q || item.label.toLowerCase().includes(q) || item.sub.toLowerCase().includes(q)
                                );

                                if (!hasil.length) {
                                    paletteList.innerHTML = '<p class="px-3 py-6 text-center text-sm text-slate-400">Menu tidak ditemukan.</p>';
                                    return;
                                }

                                paletteList.innerHTML = hasil.map(item => `
                                    <a href="${item.url}" ${item.post ? 'data-post="1"' : ''}
                                       class="flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">
                                        <span class="min-w-0">
                                            <span class="block truncate font-semibold">${item.label}</span>
                                            <span class="block truncate text-xs text-slate-400">${item.sub}</span>
                                        </span>
                                        <svg class="h-4 w-4 shrink-0 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                                        </svg>
                                    </a>
                                `).join('');

                                paletteList.querySelectorAll('[data-post]').forEach(el => {
                                    el.addEventListener('click', function (e) {
                                        e.preventDefault();
                                        const form = document.createElement('form');
                                        form.method = 'POST';
                                        form.action = this.getAttribute('href');
                                        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                                        document.body.appendChild(form);
                                        form.submit();
                                    });
                                });
                            }

                            function openPalette() {
                                if (!palette) return;
                                palette.classList.remove('palette-closed');
                                palette.classList.add('flex');
                                renderPalette('');
                                if (paletteInput) paletteInput.value = '';
                                setTimeout(() => paletteInput && paletteInput.focus(), 60);
                            }

                            function closePalette() {
                                if (!palette) return;
                                palette.classList.add('palette-closed');
                                palette.classList.remove('flex');
                            }

                            renderPalette('');
                            document.querySelectorAll('[data-palette-open]').forEach(el => el.addEventListener('click', openPalette));
                            document.querySelectorAll('[data-palette-close]').forEach(el => el.addEventListener('click', closePalette));
                            if (paletteInput) paletteInput.addEventListener('input', e => renderPalette(e.target.value));

                            document.addEventListener('keydown', function (e) {
                                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                                    e.preventDefault();
                                    if (palette && palette.classList.contains('palette-closed')) { openPalette(); } else { closePalette(); }
                                }
                                if (e.key === 'Escape') {
                                    closePalette();
                                    closeSheet();
                                }
                            });

                            /* Tabel lebar: tandai kalau masih ada kolom yang terpotong,
                               supaya petunjuk "geser ke samping" hanya muncul saat perlu. */
                            const tableScrollers = document.querySelectorAll('.table-scroll');

                            function updateTableHints() {
                                tableScrollers.forEach(el => {
                                    const overflowing = el.scrollWidth - el.clientWidth > 4;
                                    el.classList.toggle('is-overflowing', overflowing);
                                });
                            }

                            tableScrollers.forEach(el => {
                                el.addEventListener('scroll', () => {
                                    el.classList.toggle('is-scrolled', el.scrollLeft > 4);
                                }, { passive: true });
                            });

                            window.addEventListener('resize', updateTableHints);

                            /* Jalankan setelah gambar/font selesai dimuat supaya
                               lebar tabelnya sudah final saat diukur. */
                            if (document.readyState === 'complete') {
                                updateTableHints();
                            } else {
                                window.addEventListener('load', updateTableHints);
                            }

                            /* Indikator progres baca halaman */
                            const bar = document.getElementById('scroll-progress');
                            const updateBar = function () {
                                if (!bar) return;
                                const h = document.documentElement;
                                const max = h.scrollHeight - h.clientHeight;
                                bar.style.width = max > 0 ? (h.scrollTop / max) * 100 + '%' : '0%';
                            };
                            window.addEventListener('scroll', updateBar, { passive: true });
                            updateBar();

                            /* Toast notifikasi hilang sendiri biar tidak menutupi konten. */
                            document.querySelectorAll('[data-toast]').forEach(el => {
                                setTimeout(() => {
                                    el.style.transition = 'opacity .3s ease, transform .3s ease';
                                    el.style.opacity = '0';
                                    el.style.transform = 'translateY(-8px)';
                                    setTimeout(() => el.remove(), 320);
                                }, 5000);
                            });
                        })();
</script>
</body>
</html>
