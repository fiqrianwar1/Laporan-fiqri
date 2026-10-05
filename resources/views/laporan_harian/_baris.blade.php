{{--
    Satu baris laporan harian oplosan (dipakai di halaman index).
    Bentuknya sengaja seperti lembar kerja: judul berisi identitas unit,
    lalu tabel kecil berisi bahan, volume, jam kerja, dan hasil matching.
--}}
@php
    $warnaMatching = [
        'Sama'  => 'pill-emerald',
        'Mirip' => 'pill-amber',
        'Beda'  => 'pill-rose',
    ][$baris->hasil_matching] ?? 'pill-slate';
@endphp

<div class="px-4 py-3.5 sm:px-5" x-data="{ buka: false }">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-sm font-bold text-slate-900">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 text-[11px] font-bold text-white">
                        {{ $baris->plat_nomor ? strtoupper(substr($baris->plat_nomor, 0, 3)) : '—' }}
                    </span>
                    {{ $baris->plat_nomor }}
                </span>
                <span class="pill pill-blue">{{ $baris->kode_warna }}</span>
                <span class="{{ $warnaMatching }} pill">{{ $baris->hasil_matching }}</span>
            </div>
            <p class="mt-1.5 truncate text-xs text-slate-500">
                {{ $baris->tipe_mobil }}
                <span class="mx-1 text-slate-300">•</span>
                {{ \App\Support\Tanggal::panjang($baris->tanggal) }}
                <span class="mx-1 text-slate-300">•</span>
                {{ $baris->cabang_area }}
            </p>
        </div>

        <div class="text-right">
            <p class="angka text-base font-extrabold text-slate-900">{{ number_format($baris->volume_cc) }} cc</p>
            <p class="text-[11px] text-slate-500">
                {{ $baris->jam_dibuat ? \Illuminate\Support\Str::of($baris->jam_dibuat)->substr(0, 5) : '—' }}
                @if ($baris->jam_selesai)
                    &rarr; {{ \Illuminate\Support\Str::of($baris->jam_selesai)->substr(0, 5) }}
                @endif
                @if ($baris->durasi_menit !== null)
                    <span class="font-semibold text-slate-600">({{ $baris->durasi_menit }} mnt)</span>
                @endif
            </p>
        </div>
    </div>

    <div class="mt-2.5 flex flex-wrap items-center justify-between gap-2">
        <p class="min-w-0 flex-1 truncate text-xs text-slate-600">
            <span class="font-semibold text-slate-500">Bahan:</span> {{ $baris->bahan_cat }}
            @if ($baris->keterangan)
                <span class="mx-1 text-slate-300">•</span>
                <span class="text-slate-500">{{ $baris->keterangan }}</span>
            @endif
        </p>

        <div class="flex shrink-0 items-center gap-1.5">
            <a href="{{ route('laporan-harian.edit', $baris) }}" class="btn-act border border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:text-blue-700">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h6m-3-3v6M4 7v12h12V9M4 7l4-4m8 14-4 4H8v-4l4-4Z"/>
                </svg>
                Edit
            </a>
            <form method="POST" action="{{ route('laporan-harian.destroy', $baris) }}"
                  onsubmit="return confirm('Hapus baris laporan ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-act border border-rose-100 bg-rose-50 text-rose-600 hover:border-rose-200 hover:bg-rose-100">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V5h6v2m-8 0 1 12h8l1-12"/>
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>
