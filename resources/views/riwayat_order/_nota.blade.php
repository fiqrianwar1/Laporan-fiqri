@php
    $bisaAksi = $bisaAksi ?? false;
    $rupiah = fn ($angka) => 'Rp ' . number_format((float) $angka, 0, ',', '.');
    $kartuKosong = $jumlahItem === 0 ? 'shadow-none' : '';
@endphp

{{-- Satu nota = satu kartu. Detail itemnya bisa dibuka-tutup supaya
     daftar tetap ringkas walau satu nota isinya banyak barang. --}}
<details class="group bg-white/70 transition hover:bg-white {{ $loop->first ? '' : 'border-t border-slate-200/60' }}" {{ $jumlahItem > 0 && $jumlahItem <= 2 ? 'open' : '' }}>
    <summary class="flex cursor-pointer list-none items-center gap-3 px-4 py-4 marker:hidden sm:px-5">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition group-open:rotate-90 group-open:bg-indigo-50 group-open:text-indigo-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
            </svg>
        </span>

        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold text-slate-800">{{ $nota['nomor'] ?? 'Tanpa nomor' }}</p>
            <p class="truncate text-[11px] text-slate-500">
                {{ $nota['tanggal']?->format('d/m/Y') }}
                @if ($nota['adaNomorUrut'])
                    &middot; item #{{ $nota['items']->min('nomor_urut') }}&ndash;{{ $nota['items']->max('nomor_urut') }}
                @endif
                &middot; <span class="font-bold text-indigo-600">{{ $jumlahItem }} item</span>
            </p>
            @php $cabangNota = $nota['items']->first()->cabang_area; @endphp
            @if ($cabangNota)
                <span class="mt-1.5 inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700 ring-1 ring-indigo-100">
                    <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                    </svg>
                    {{ $cabangNota }}
                </span>
            @endif
        </div>

        <div class="shrink-0 text-right">
            <p class="text-sm font-bold text-slate-900">{{ $rupiah($nota['total']) }}</p>
            <p class="text-[11px] text-slate-500">
                {{ number_format($nota['items']->sum('qty')) }} {{ $labelQty }}
            </p>
        </div>
    </summary>

    <div class="overflow-x-auto overscroll-x-contain px-4 pb-4 sm:px-5">
        <table class="w-full min-w-[420px] border-collapse text-xs">
            <thead>
                <tr class="text-left text-[10px] uppercase tracking-wide text-slate-400">
                    <th class="w-8 border-b border-slate-200 py-2 font-bold">#</th>
                    <th class="border-b border-slate-200 py-2 font-bold">{{ $labelNama }}</th>
                    <th class="border-b border-slate-200 py-2 text-right font-bold">Qty</th>
                    <th class="border-b border-slate-200 py-2 text-right font-bold">{{ $labelHarga }}</th>
                    @if ($kolomDiskon)
                        <th class="border-b border-slate-200 py-2 text-right font-bold">Diskon</th>
                    @endif
                    <th class="border-b border-slate-200 py-2 text-right font-bold">Total</th>
                    @if ($bisaAksi)
                        <th class="border-b border-slate-200 py-2 text-center font-bold">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($nota['items'] as $item)
                    <tr class="border-b border-slate-100/80 last:border-0">
                        <td class="py-2 text-slate-400">{{ $loop->iteration }}</td>
                        <td class="py-2">
                            <p class="font-semibold text-slate-800">{{ $item->nama_barang }}</p>
                            <p class="text-[11px] text-slate-400">
                                {{ $item->kode_barang ?? 'Tanpa kode' }}
                                @if ($item->nomor_urut)
                                    &middot; item #{{ $item->nomor_urut }}
                                @endif
                                &middot; {{ $item->satuan }}
                            </p>
                        </td>
                        <td class="py-2 text-right font-semibold text-slate-700">{{ number_format($item->qty) }}</td>
                        <td class="py-2 text-right text-slate-600">{{ $rupiah($item->harga_satuan) }}</td>
                        @if ($kolomDiskon)
                            <td class="py-2 text-right font-semibold {{ $item->diskon_persen > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ number_format($item->diskon_persen, 2) }}%
                            </td>
                        @endif
                        <td class="py-2 text-right font-bold text-slate-900">{{ $rupiah($item->total_item) }}</td>
                        @if ($bisaAksi)
                            <td class="py-2">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('riwayat-order.edit', $item) }}"
                                       class="act bg-amber-50 text-amber-700 hover:bg-amber-100" title="Edit item ini">Edit</a>
                                    <form action="{{ route('riwayat-order.destroy', $item) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus item ini dari nota? Data yang dihapus tidak dapat dikembalikan.')">
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
                    <td colspan="{{ 4 + ($kolomDiskon ? 1 : 2) }}" class="py-2 text-right text-[11px] font-bold uppercase tracking-wide text-slate-500">
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
