{{--
    Satu tanggal laporan harian oplosan (dipakai di halaman index).

    Bentuknya mengikuti kartu nota di laporan oplosan: kepala berisi tanggal,
    cabang, jumlah pekerjaan & volume, lalu rincian pekerjaan hari itu dibuka
    lewat tombol. Tiap pekerjaan tetap bisa diubah / dihapus dari sini.
--}}
@php
    use App\Support\Tanggal;

    // Cabang tiap pekerjaan bisa beda; yang ditonjolkan cabang pertamanya.
    $cabangHari = $hari['items']->pluck('cabang_area')->filter()->unique()->values();

    // Platnya dirangkum di kepala kartu supaya unit hari itu terlihat sekilas.
    $platHari = $hari['items']->pluck('plat_nomor')->filter()->unique()->values();
    $platRingkas = $platHari->take(3)->implode(' · ')
        . ($platHari->count() > 3 ? ' +' . ($platHari->count() - 3) . ' lainnya' : '');

    $persenSama = $hari['jumlahItem'] > 0
        ? round(($hari['sama'] / $hari['jumlahItem']) * 100)
        : 0;
@endphp

<div class="bg-white/70 transition hover:bg-white {{ $loop->first ? '' : 'border-t border-slate-200/60' }}">
    <div class="flex flex-wrap items-center gap-3 px-4 py-4 sm:px-5">
        {{-- Tanggal ditulis sebagai angka besar + bulan kecil, biar tanggal 1
             langsung ketahuan tanpa harus dibaca panjang. --}}
        <div class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/30">
            <span class="text-base font-extrabold leading-none">{{ $hari['tanggal']?->format('d') ?? '—' }}</span>
            <span class="text-[9px] font-semibold uppercase leading-tight">{{ $hari['tanggal']?->translatedFormat('M') }}</span>
        </div>

        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold text-slate-800">{{ Tanggal::panjang($hari['tanggal']) }}</p>
            <p class="truncate text-[11px] text-slate-500">
                <span class="font-bold text-blue-600">{{ $hari['jumlahItem'] }} pekerjaan</span>
                &middot; {{ number_format($hari['volume']) }} cc
                @if ($platRingkas)
                    &middot; {{ $platRingkas }}
                @endif
            </p>
            @if ($cabangHari->isNotEmpty())
                <span class="mt-1.5 inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700 ring-1 ring-blue-100">
                    <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                    </svg>
                    {{ $cabangHari->implode(', ') }}
                </span>
            @endif
        </div>

        <div class="shrink-0 text-right">
            <p class="angka text-base font-extrabold text-slate-900">{{ number_format($hari['volume']) }} cc</p>
            <p class="text-[11px] text-slate-500">
                @if ($hari['durasi'] > 0)
                    {{ number_format($hari['durasi']) }} mnt &middot;
                @endif
                <span class="{{ $persenSama === 100 ? 'font-bold text-emerald-600' : 'font-semibold text-amber-600' }}">
                    {{ $persenSama }}% sama
                </span>
            </p>
        </div>
    </div>

    {{-- Tombol buka/tutup rincian pekerjaan hari ini. --}}
    <details class="group">
        <summary class="btn-detail cursor-pointer list-none marker:hidden">
            <span class="transition group-open:rotate-90">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                </svg>
            </span>
            <span class="group-open:hidden">Buka rincian pekerjaan ({{ $hari['jumlahItem'] }})</span>
            <span class="hidden group-open:inline">Tutup rincian pekerjaan</span>
        </summary>

        <div class="divide-y divide-slate-100 border-t border-slate-200/60 bg-slate-50/40">
            @foreach ($hari['items'] as $baris)
                @include('laporan_harian._baris', ['baris' => $baris])
            @endforeach
        </div>
    </details>
</div>
