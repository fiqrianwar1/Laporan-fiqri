{{--
    Satu surat jalan = satu kartu (dipakai di halaman index).

    Susunannya sama dengan kartu nota di riwayat order: baris ringkasan
    (nomor surat, tanggal, total) -> tombol buka rincian -> baru tabel
    barang. Tiap barang tetap bisa diubah / dihapus dari sini.
--}}
@php
    use App\Support\Tanggal;

    // Cabang tiap barang bisa beda; semuanya ditampilkan di kepala kartu.
    $cabangSurat = $surat['items']->pluck('cabang_area')->filter()->unique()->values();

    // Kode barang dirangkum di kepala kartu biar isinya terlihat sekilas.
    $kodeBarang = $surat['items']->pluck('kode_barang')->filter()->unique()->values();
    $kodeRingkas = $kodeBarang->take(3)->implode(' · ')
        . ($kodeBarang->count() > 3 ? ' +' . ($kodeBarang->count() - 3) . ' lainnya' : '');

    $totalJumlah = $surat['items']->sum('jumlah');

    // Foto print satu surat. Tiap barang sering di-upload foto yang isinya
    // sama, jadi penyaringan pakai isi filenya - satu foto cuma tampil sekali.
    $fotoPrint = \App\Support\FotoNota::unik($surat['items'], 'foto_print', 6);

    // Nomor urut surat: muncul kalau nomor suratnya kembar, supaya tidak ada
    // dua kartu yang terlihat persis sama.
    $nomorKartu = $nomorSurat[$surat['kunci']] ?? $loop->iteration;
    $nomorKembar = $suratHalaman
        ->filter(fn ($s) => ($s['nomor'] ?? null) === ($surat['nomor'] ?? null))
        ->count() > 1;
@endphp

<div class="bg-white/70 transition hover:bg-white {{ $loop->first ? '' : 'border-t border-slate-200/60' }}">
    <div class="flex flex-wrap items-center gap-3 px-4 py-4 sm:px-5">
        <span class="relative flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            @if ($nomorKembar)
                <span class="absolute -right-1.5 -top-1.5 flex h-5 min-w-[20px] items-center justify-center rounded-full bg-amber-400 px-1 text-[10px] font-extrabold text-amber-950 ring-2 ring-white"
                      title="Nomor surat ini muncul lebih dari sekali di halaman ini">
                    {{ $nomorKartu }}
                </span>
            @endif
        </span>

        <div class="min-w-0 flex-1">
            <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm font-bold text-slate-800">
                @if ($nomorKembar)
                    <span class="inline-flex shrink-0 items-center rounded-md bg-amber-50 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-700 ring-1 ring-amber-100"
                          title="Urutan surat ini pada periode yang sedang ditampilkan">
                        Surat {{ $nomorKartu }}
                    </span>
                @endif
                <span class="truncate">{{ $surat['nomor'] ?? 'Tanpa nomor' }}</span>
            </p>
            <p class="truncate text-[11px] text-slate-500">
                {{ Tanggal::panjang($surat['tanggal']) }}
                @if ($kodeRingkas)
                    &middot; {{ $kodeRingkas }}
                @endif
                @if ($surat['adaNomorUrut'])
                    &middot; item #{{ $surat['items']->min('nomor_urut') }}&ndash;{{ $surat['items']->max('nomor_urut') }}
                @endif
                &middot; <span class="font-bold text-indigo-600">{{ $jumlahItem }} barang</span>
            </p>
            @if ($cabangSurat->isNotEmpty())
                <span class="mt-1.5 inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700 ring-1 ring-indigo-100">
                    <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                    </svg>
                    {{ $cabangSurat->implode(', ') }}
                </span>
            @endif
        </div>

        <div class="shrink-0 text-right">
            <p class="angka-nota">{{ number_format($totalJumlah) }}</p>
            <p class="text-[11px] text-slate-500">total jumlah</p>
        </div>
    </div>

    {{-- Tombol buka/tutup rincian. Ditaruh di luar tabel supaya tabelnya
         tetap valid (tidak ada elemen tabel di dalam tombol). --}}
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
        @if ($fotoPrint->isNotEmpty())
            {{-- Bukti foto print, ukurannya diseragamkan supaya rapi. --}}
            <div class="mb-4 flex flex-wrap items-center gap-2 rounded-xl border border-slate-200/80 bg-slate-50/60 p-2.5">
                @foreach ($fotoPrint as $path)
                    <a href="{{ Storage::url($path) }}" target="_blank"
                       class="group/foto relative block h-20 w-20 shrink-0 overflow-hidden rounded-lg ring-1 ring-slate-200 transition hover:ring-2 hover:ring-indigo-400"
                       title="Buka foto print ini">
                        <img src="{{ Storage::url($path) }}" alt="Foto print {{ $surat['nomor'] }}"
                             loading="lazy"
                             class="h-full w-full object-cover transition duration-200 group-hover/foto:scale-105">
                        <span class="absolute inset-x-0 bottom-0 bg-slate-900/65 py-0.5 text-center text-[9px] font-bold uppercase tracking-wide text-white">
                            Foto
                        </span>
                    </a>
                @endforeach

                <a href="{{ Storage::url($fotoPrint->first()) }}" target="_blank"
                   class="ml-auto inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-2 text-[11px] font-bold text-indigo-600 shadow-sm ring-1 ring-indigo-100 transition hover:bg-indigo-50">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/>
                    </svg>
                    {{ $fotoPrint->count() > 1 ? 'Lihat ' . $fotoPrint->count() . ' Foto' : 'Lihat Foto' }}
                </a>
            </div>
        @endif

        <table class="table-item min-w-[880px]">
            <thead>
                <tr>
                    <th class="td-no">#</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang / Cat</th>
                    <th>Kemasan</th>
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
                    {{-- 4 kolom sebelum Jumlah (#, Kode, Nama, Kemasan). --}}
                    <td colspan="4" class="angka-label border-t-2 border-slate-200 text-right">
                        Total Jumlah Barang
                    </td>
                    <td class="border-t-2 border-slate-200 text-right"><span class="angka-total">{{ number_format($totalJumlah) }}</span></td>
                    {{-- Sisa: Asal Penyimpanan, Keterangan (+ Aksi kalau ada). --}}
                    <td class="border-t-2 border-slate-200" colspan="{{ $bisaAksi ? 3 : 2 }}"></td>
                </tr>
            </tfoot>
        </table>
        </div>
    </details>
</div>
