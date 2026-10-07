{{--
    Satu surat jalan = satu kartu (dipakai di halaman index).

    Bentuknya mengikuti kartu nota di laporan oplosan: kepala berisi nomor
    surat, tanggal, tujuan & jumlah barang, lalu rincian barangnya dibuka
    lewat tombol. Tiap barang tetap bisa diubah / dihapus dari sini.
--}}
@php
    use App\Support\Tanggal;

    // Cabang tiap barang bisa beda; yang ditonjolkan cabang pertamanya.
    $cabangSurat = $surat['items']->pluck('cabang_area')->filter()->unique()->values();

    // Kode barang dirangkum di kepala kartu biar isinya terlihat sekilas.
    $kodeBarang = $surat['items']->pluck('kode_barang')->filter()->unique()->values();
    $kodeRingkas = $kodeBarang->take(3)->implode(' · ')
        . ($kodeBarang->count() > 3 ? ' +' . ($kodeBarang->count() - 3) . ' lainnya' : '');

    $totalJumlah = $surat['items']->sum('jumlah');
@endphp

<div class="bg-white/70 transition hover:bg-white {{ $loop->first ? '' : 'border-t border-slate-200/60' }}">
    <div class="flex flex-wrap items-center gap-3 px-4 py-4 sm:px-5">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/30">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </span>

        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold text-slate-800">
                {{ $surat['nomor'] ?? 'Tanpa nomor' }}
            </p>
            <p class="truncate text-[11px] text-slate-500">
                {{ Tanggal::panjang($surat['tanggal']) }}
                &middot; <span class="font-bold text-blue-600">{{ $jumlahItem }} barang</span>
                @if ($kodeRingkas)
                    &middot; {{ $kodeRingkas }}
                @endif
                @if ($surat['adaNomorUrut'])
                    &middot; item #{{ $surat['items']->min('nomor_urut') }}&ndash;{{ $surat['items']->max('nomor_urut') }}
                @endif
            </p>
            <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                @if ($cabangSurat->isNotEmpty())
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-100">
                        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                        </svg>
                        {{ $cabangSurat->implode(', ') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="shrink-0 text-right">
            <p class="angka text-base font-extrabold text-slate-900">{{ number_format($totalJumlah) }}</p>
            <p class="text-[11px] text-slate-500">total jumlah</p>
        </div>
    </div>

    {{-- Tombol buka/tutup rincian barang. --}}
    <details class="group">
        <summary class="btn-detail cursor-pointer list-none marker:hidden">
            <span class="transition group-open:rotate-90">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                </svg>
            </span>
            <span class="group-open:hidden">Buka rincian barang ({{ $jumlahItem }})</span>
            <span class="hidden group-open:inline">Tutup rincian barang</span>
        </summary>

        <div class="table-wrap overflow-x-auto overscroll-x-contain">
            <table class="table-item min-w-[820px]">
                <thead>
                    <tr>
                        <th class="td-no">No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang / Cat</th>
                        <th>Kemasan Barang</th>
                        <th class="text-right">Jumlah</th>
                        <th>Asal Penyimpanan</th>
                        <th>Keterangan</th>
                        @if ($bisaAksi)
                            <th class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($surat['items'] as $item)
                        <tr>
                            <td class="td-no">{{ $item->nomor_urut ?? $loop->iteration }}</td>
                            <td class="font-semibold text-slate-700">{{ $item->kode_barang ?: '—' }}</td>
                            <td class="font-semibold text-slate-800">{{ $item->nama_barang }}</td>
                            <td>{{ $item->kemasan_barang ?: '—' }}</td>
                            <td class="text-right"><span class="angka-sel">{{ number_format($item->jumlah) }}</span></td>
                            <td>{{ $item->asal_penyimpanan ?: '—' }}</td>
                            <td class="text-slate-500">{{ $item->keterangan ?: '—' }}</td>
                            @if ($bisaAksi)
                                <td>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('surat-jalan.edit', $item) }}"
                                           class="btn-act bg-amber-50 text-amber-700 hover:bg-amber-100" title="Edit barang ini">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <form action="{{ route('surat-jalan.destroy', $item) }}" method="POST"
                                              onsubmit="return confirm('Yakin hapus barang ini dari surat? Data yang dihapus tidak bisa dikembalikan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-act bg-rose-50 text-rose-600 hover:bg-rose-100" title="Hapus barang ini">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0v12a1 1 0 001 1h6a1 1 0 001-1V7"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="{{ $bisaAksi ? 4 : 3 }}" class="angka-label border-t-2 border-slate-200 text-right">
                            Total Jumlah Barang
                        </td>
                        <td class="border-t-2 border-slate-200 text-right"><span class="angka-total">{{ number_format($totalJumlah) }}</span></td>
                        <td class="border-t-2 border-slate-200" colspan="{{ $bisaAksi ? 3 : 2 }}"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </details>
</div>
