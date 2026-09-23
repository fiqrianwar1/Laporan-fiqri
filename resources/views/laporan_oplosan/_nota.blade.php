@php
    $rupiah = fn ($angka) => 'Rp ' . number_format((float) $angka, 0, ',', '.');
    $platUtama = $nota['items']->first()->no_plat;
    $unitSama = $nota['items']->every(fn ($item) => $item->no_plat === $platUtama);
@endphp

{{-- Satu nota = satu kartu. Kalau nota itu memuat beberapa bahan,
     rinciannya dibuka lewat kartu ini, bukan jadi baris terpisah. --}}
<details class="group bg-white/70 transition hover:bg-white {{ $loop->first ? '' : 'border-t border-slate-200/60' }}" {{ $jumlahItem > 0 && $jumlahItem <= 2 ? 'open' : '' }}>
    <summary class="flex cursor-pointer list-none items-center gap-3 px-4 py-4 marker:hidden sm:px-5">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition group-open:rotate-90 group-open:bg-blue-50 group-open:text-blue-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
            </svg>
        </span>

        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold text-slate-800">{{ $platUtama ?? 'Tanpa plat' }}</p>
            <p class="truncate text-[11px] text-slate-500">
                {{ $nota['nomor'] ?? 'Tanpa nomor' }} &middot; {{ $nota['tanggal']?->format('d/m/Y') }}
                @if ($nota['adaNomorUrut'])
                    &middot; item #{{ $nota['items']->min('nomor_urut') }}&ndash;{{ $nota['items']->max('nomor_urut') }}
                @endif
                &middot; <span class="font-bold text-blue-600">{{ $jumlahItem }} item</span>
            </p>
            @php $cabangNota = $nota['items']->first()->cabang_area; @endphp
            @if ($cabangNota)
                <span class="mt-1.5 inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700 ring-1 ring-blue-100">
                    <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                    </svg>
                    {{ $cabangNota }}
                </span>
            @endif
        </div>

        <div class="shrink-0 text-right">
            <p class="text-sm font-bold text-slate-900">{{ $rupiah($nota['total']) }}</p>
            <p class="text-[11px] text-slate-500">{{ number_format($nota['items']->sum('qty_cc')) }} CC/LTR</p>
        </div>
    </summary>

    <div class="overflow-x-auto overscroll-x-contain px-4 pb-4 sm:px-5">
        <table class="w-full min-w-[420px] border-collapse text-xs">
            <thead>
                <tr class="text-left text-[10px] uppercase tracking-wide text-slate-400">
                    <th class="w-8 border-b border-slate-200 py-2 font-bold">#</th>
                    <th class="border-b border-slate-200 py-2 font-bold">Warna / Unit</th>
                    @if (! $unitSama)
                        <th class="border-b border-slate-200 py-2 font-bold">No. Plat</th>
                    @endif
                    <th class="border-b border-slate-200 py-2 font-bold">Bahan</th>
                    <th class="border-b border-slate-200 py-2 text-right font-bold">Qty</th>
                    <th class="border-b border-slate-200 py-2 text-right font-bold">Harga</th>
                    <th class="border-b border-slate-200 py-2 text-right font-bold">Harga/CC</th>
                    @if ($bisaAksi)
                        <th class="border-b border-slate-200 py-2 text-center font-bold">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($nota['items'] as $item)
                    <tr class="border-b border-slate-100/80 last:border-0">
                        <td class="py-2 text-slate-400">{{ $loop->iteration }}</td>
                        <td class="py-2 font-semibold text-slate-800">{{ $item->kode_warna_unit }}</td>
                        @if (! $unitSama)
                            <td class="py-2 text-slate-600">{{ $item->no_plat }}</td>
                        @endif
                        <td class="py-2">
                            <span class="font-semibold text-slate-800">{{ $item->rincian_bahan }}</span>
                            @if ($item->nomor_urut)
                                <span class="text-[11px] text-slate-400">&middot; item #{{ $item->nomor_urut }}</span>
                            @endif
                        </td>
                        <td class="py-2 text-right font-semibold text-slate-700">{{ number_format($item->qty_cc) }}</td>
                        <td class="py-2 text-right text-slate-600">{{ $rupiah($item->harga_nota) }}</td>
                        <td class="py-2 text-right text-slate-500">
                            {{ $item->qty_cc > 0 ? $rupiah($item->harga_nota / $item->qty_cc) : '-' }}
                        </td>
                        @if ($bisaAksi)
                            <td class="py-2">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('laporan-oplosan.edit', $item) }}"
                                       class="act bg-amber-50 text-amber-700 hover:bg-amber-100" title="Edit item ini">Edit</a>
                                    <form action="{{ route('laporan-oplosan.destroy', $item) }}" method="POST"
                                          onsubmit="return confirm('Yakin hapus item ini dari nota? Data yang dihapus tidak bisa dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="act bg-rose-50 text-rose-600 hover:bg-rose-100" title="Hapus item ini">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="{{ 6 + ($unitSama ? 0 : 1) }}" class="py-2 text-right text-[11px] font-bold uppercase tracking-wide text-slate-500">
                        Total Nota
                    </td>
                    <td class="py-2 text-right text-sm font-bold text-slate-900">{{ $rupiah($nota['total']) }}</td>
                    @if ($bisaAksi)
                        <td></td>
                    @endif
                </tr>
            </tfoot>
        </table>
    </div>
</details>
