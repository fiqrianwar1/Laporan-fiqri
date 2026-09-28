@php
    use App\Support\Tanggal;
    use App\Support\Rupiah;

    $bisaAksi = $bisaAksi ?? false;

    // Nilai tidak dibulatkan: sen aslinya (,50 / ,60) tetap ditulis supaya
    // angkanya cocok dengan faktur.
    $rupiah = fn ($angka) => Rupiah::format($angka);
    $kartuKosong = $jumlahItem === 0 ? 'shadow-none' : '';

    // Kolom Diskon & Nominal: riwayat order menampilkan persen + rupiah
    // potongannya; laporan oplosan tidak memakai diskon sama sekali.
    $kolomDiskon = $kolomDiskon ?? false;
    $kolomNominalDiskon = $kolomNominalDiskon ?? false;

    // Foto bukti faktur. Tiap item sering di-upload foto faktur yang sama,
    // jadi penyaringan pakai isi filenya - satu foto cuma tampil sekali.
    $fotoNota = \App\Support\FotoNota::unik($nota['items'], 'foto_faktur', 6);
@endphp

{{-- Satu nota = satu kartu. Detail itemnya bisa dibuka-tutup supaya
     daftar tetap ringkas walau satu nota isinya banyak barang.
     Susunannya: baris ringkasan (tanggal, nomor, total) -> tombol
     buka rincian -> baru tabel item, jadi tidak ada tabel yang
     tersembunyi di dalam elemen yang bisa diklik. --}}
<div class="bg-white/70 transition hover:bg-white {{ $loop->first ? '' : 'border-t border-slate-200/60' }}">
    <div class="flex flex-wrap items-center gap-3 px-4 py-4 sm:px-5">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </span>

        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold text-slate-800">{{ Tanggal::panjang($nota['tanggal']) }}</p>
            <p class="truncate text-[11px] text-slate-500">
                {{ $nota['nomor'] ?? 'Tanpa nomor' }}
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
            <p class="angka-nota">{{ $rupiah($nota['total']) }}</p>
            <p class="text-[11px] text-slate-500">
                {{ number_format($nota['items']->sum('qty')) }} {{ $labelQty }}
            </p>
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
            <span class="group-open:hidden">Buka rincian item ({{ $jumlahItem }})</span>
            <span class="hidden group-open:inline">Tutup rincian item</span>
        </summary>

        <div class="table-wrap overflow-x-auto overscroll-x-contain">
        @if ($fotoNota->isNotEmpty())
            {{-- Bukti foto faktur, ukurannya diseragamkan supaya rapi. --}}
            <div class="mb-4 flex flex-wrap items-center gap-2 rounded-xl border border-slate-200/80 bg-slate-50/60 p-2.5">
                @foreach ($fotoNota as $path)
                    <a href="{{ Storage::url($path) }}" target="_blank"
                       class="group/foto relative block h-20 w-20 shrink-0 overflow-hidden rounded-lg ring-1 ring-slate-200 transition hover:ring-2 hover:ring-indigo-400"
                       title="Buka foto faktur ini">
                        <img src="{{ Storage::url($path) }}" alt="Foto faktur {{ $nota['nomor'] }}"
                             loading="lazy"
                             class="h-full w-full object-cover transition duration-200 group-hover/foto:scale-105">
                        <span class="absolute inset-x-0 bottom-0 bg-slate-900/65 py-0.5 text-center text-[9px] font-bold uppercase tracking-wide text-white">
                            Foto
                        </span>
                    </a>
                @endforeach

                <a href="{{ Storage::url($fotoNota->first()) }}" target="_blank"
                   class="ml-auto inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-2 text-[11px] font-bold text-indigo-600 shadow-sm ring-1 ring-indigo-100 transition hover:bg-indigo-50">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/>
                    </svg>
                    {{ $fotoNota->count() > 1 ? 'Lihat ' . $fotoNota->count() . ' Foto' : 'Lihat Foto' }}
                </a>
            </div>
        @endif

        <table class="table-item min-w-[720px]">
            <thead>
                <tr>
                    <th class="td-no">#</th>
                    <th>{{ $labelNama }}</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">{{ $labelHarga }}</th>
                    @if ($kolomDiskon)
                        <th class="text-right">Diskon</th>
                    @endif
                    @if ($kolomNominalDiskon)
                        <th class="text-right">Nominal Diskon</th>
                    @endif
                    <th class="text-right">Total Item</th>
                    @if ($bisaAksi)
                        <th class="text-center">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($nota['items'] as $item)
                    <tr>
                        <td class="td-no">{{ $loop->iteration }}</td>
                        <td>
                            <p class="font-semibold text-slate-800">{{ $item->nama_barang }}</p>
                            <p class="text-[11px] text-slate-400">
                                {{ $item->kode_barang ?? 'Tanpa kode' }}
                                @if ($item->nomor_urut)
                                    &middot; item #{{ $item->nomor_urut }}
                                @endif
                                &middot; {{ $item->satuan }}
                            </p>
                        </td>
                        <td class="text-right"><span class="angka-kecil text-slate-700">{{ number_format($item->qty) }}</span></td>
                        <td class="text-right"><span class="angka-kecil">{{ $rupiah($item->harga_satuan) }}</span></td>
                        @if ($kolomDiskon)
                            <td class="text-right font-semibold {{ $item->diskon_persen > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ number_format($item->diskon_persen, 2, ',', '.') }}%
                            </td>
                        @endif
                        @if ($kolomNominalDiskon)
                            @php $nominalDiskon = (float) $item->nominal_diskon; @endphp
                            {{-- Tanpa diskon tetap ditulis Rp 0 (bukan "-") supaya
                                 kolomnya sejajar dengan nominal di faktur. --}}
                            <td class="text-right">
                                <span class="angka-kecil {{ $nominalDiskon > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                    {{ Rupiah::formatRingkas($nominalDiskon) }}
                                </span>
                            </td>
                        @endif
                        <td class="text-right"><span class="angka-sel">{{ $rupiah($item->total_item) }}</span></td>
                        @if ($bisaAksi)
                            <td>
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('riwayat-order.edit', $item) }}"
                                       class="btn-act bg-amber-50 text-amber-700 hover:bg-amber-100" title="Edit item ini">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('riwayat-order.destroy', $item) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus item ini dari nota? Data yang dihapus tidak dapat dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-act bg-rose-50 text-rose-600 hover:bg-rose-100" title="Hapus item ini">
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
                @php $diskonNota = $nota['items']->sum(fn ($item) => (float) $item->nominal_diskon); @endphp
                @if ($diskonNota > 0)
                    {{-- Baris rekapan nota: total kotor - diskon = total nota,
                         jadi angkanya bisa dicocokkan dengan faktur. --}}
                    <tr>
                        <td colspan="{{ 4 + ($kolomDiskon ? 1 : 0) + ($kolomNominalDiskon ? 1 : 0) }}"
                            class="angka-label text-right">
                            Total sebelum diskon
                        </td>
                        <td class="text-right">
                            <span class="angka-kecil text-slate-700">
                                {{ $rupiah($nota['items']->sum(fn ($item) => (float) $item->total_kotor)) }}
                            </span>
                        </td>
                        @if ($bisaAksi)
                            <td></td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="{{ 4 + ($kolomDiskon ? 1 : 0) + ($kolomNominalDiskon ? 1 : 0) }}"
                            class="angka-label text-right">
                            Diskon
                        </td>
                        <td class="text-right">
                            <span class="angka-kecil text-emerald-600">&minus; {{ $rupiah($diskonNota) }}</span>
                        </td>
                        @if ($bisaAksi)
                            <td></td>
                        @endif
                    </tr>
                @endif
                <tr>
                    <td colspan="{{ 4 + ($kolomDiskon ? 1 : 0) + ($kolomNominalDiskon ? 1 : 0) }}" class="angka-label border-t-2 border-slate-200 text-right">
                        Grand Total Nota
                    </td>
                    <td class="border-t-2 border-slate-200 text-right"><span class="angka-total">{{ $rupiah($nota['total']) }}</span></td>
                    @if ($bisaAksi)
                        <td class="border-t-2 border-slate-200"></td>
                    @endif
                </tr>
            </tfoot>
        </table>
        </div>
    </details>
</div>
