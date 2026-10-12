{{--
    Baris chip pilih bulan.

    Dipakai di atas form filter periode: pengguna bisa pindah bulan
    dengan sekali klik, dan bulan yang sedang aktif ditandai warna
    (class .is-aktif dari resources/css/app.css).

    Chip ini SENGAJA dibuat sebagai link biasa (bukan mengubah nilai
    <select> lewat JS) supaya halaman tetap bisa dipakai walau JS
    dimatikan, dan tidak mengubah cara kerja filter yang sudah ada.

    Props:
      - $aksi  : URL halaman yang sedang dibuka (route index-nya).
      - $bulan : nilai bulan aktif (string '01'..'12', atau kosong).
      - $tahun : nilai tahun aktif, dipertahankan saat pindah bulan.
      - $cabang: nilai cabang aktif, dipertahankan saat pindah bulan.
--}}
@props([
    'aksi',
    'bulan' => '',
    'tahun' => '',
    'cabang' => '',
    'namaBulan' => ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'],
])

@php
    $bulanAktif = (string) ($bulan ?? '');
    // Parameter yang ikut dibawa saat pindah bulan (tahun & cabang).
    $teruskan = array_filter([
        'tahun'  => $tahun,
        'cabang' => $cabang,
    ], fn ($v) => filled($v));
@endphp

<div class="mt-2.5 flex flex-wrap items-center gap-x-2 gap-y-2 border-t border-slate-200/60 pt-2.5">
    <span class="mr-0.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Pilih cepat</span>

    <div class="bulan-rail">
        <a href="{{ $aksi . ($teruskan ? '?' . http_build_query($teruskan) : '') }}"
           title="Tampilkan semua bulan"
           class="bulan-chip {{ $bulanAktif === '' ? 'is-aktif' : '' }}">
            Semua
        </a>

        @foreach ($namaBulan as $val => $label)
            @php
                $aktif = $bulanAktif === (string) $val;
                $query = array_merge($teruskan, ['bulan' => $val]);
            @endphp
            <a href="{{ $aksi . '?' . http_build_query($query) }}"
               title="Filter bulan {{ $label }}"
               class="bulan-chip {{ $aktif ? 'is-aktif' : '' }}"
               @if ($aktif) aria-current="true" @endif>
                <svg class="bulan-cek h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>
