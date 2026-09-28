@php
    use App\Support\Tanggal;
    use App\Support\Rupiah;

    // Nilai tidak dibulatkan - sen aslinya tetap ditulis apa adanya.
    $rupiah = fn ($angka) => Rupiah::format($angka);

    // Kolom No. Plat selalu ditampilkan selama ada item yang punya plat.
    // Sebelumnya kolom ini dikunci dengan "platnya beda-beda", dan itu keliru:
    // nota berisi satu item (atau semua itemnya plume sama) jadi ikut
    // kehilangan kolom padahal platnya terisi. Kalau unitnya seragam, kolomnya
    // tetap tampil dan isinya sama - justru lebih jelas dibaca.
    $adaPlat = $nota['items']->contains(fn ($item) => filled($item->no_plat));

    // Satu nota bisa memuat beberapa unit sekaligus, jadi semua platnya
    // dikumpulkan di baris kedua supaya tidak ada yang tersembunyi.
    $platNota = $nota['items']->pluck('no_plat')->filter()->unique()->values();
    $platRingkas = $platNota->take(2)->implode(' · ')
        . ($platNota->count() > 2 ? ' +' . ($platNota->count() - 2) . ' lainnya' : '');

    // Foto bukti nota yang menempel pada item-item nota ini. Tiap item sering
    // di-upload foto nota yang sama, jadi penyaringan pakai isi filenya -
    // hasilnya satu foto cuma tampil sekali.
    $fotoNota = \App\Support\FotoNota::unik($nota['items'], 'foto_nota', 6);
@endphp

{{-- Satu nota = satu kartu. Kalau nota itu memuat beberapa bahan,
     rinciannya dibuka lewat tombol di bawah baris ringkasan - bukan
     dengan menebak bahwa barisnya bisa diklik. --}}
<div class="bg-white/70 transition hover:bg-white {{ $loop->first ? '' : 'border-t border-slate-200/60' }}">
    <div class="flex flex-wrap items-center gap-3 px-4 py-4 sm:px-5">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
            </svg>
        </span>

        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-bold text-slate-800">{{ Tanggal::panjang($nota['tanggal']) }}</p>
            <p class="truncate text-[11px] text-slate-500">
                {{ $nota['nomor'] ?? 'Tanpa nomor' }}
                @if ($platRingkas)
                    &middot; {{ $platRingkas }}
                @endif
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
            <p class="angka-nota">{{ $rupiah($nota['total']) }}</p>
            <p class="text-[11px] text-slate-500">{{ number_format($nota['items']->sum('qty_cc')) }} CC/LTR</p>
        </div>
    </div>

    {{-- Tombol buka/tutup rincian bahan. --}}
    <details class="group">
        <summary class="btn-detail cursor-pointer list-none marker:hidden">
            <span class="transition group-open:rotate-90">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                </svg>
            </span>
            <span class="group-open:hidden">Buka rincian bahan ({{ $jumlahItem }})</span>
            <span class="hidden group-open:inline">Tutup rincian bahan</span>
        </summary>

        <div class="table-wrap overflow-x-auto overscroll-x-contain">
        @if ($fotoNota->isNotEmpty())
            {{-- Bukti foto nota. Disamakan ukurannya supaya tidak ada yang
                 menonjol kalau notanya difoto dengan resolusi berbeda. --}}
            <div class="mb-4 flex flex-wrap items-center gap-2 rounded-xl border border-slate-200/80 bg-slate-50/60 p-2.5">
                @foreach ($fotoNota as $path)
                    <a href="{{ Storage::url($path) }}" target="_blank"
                       class="group/foto relative block h-20 w-20 shrink-0 overflow-hidden rounded-lg ring-1 ring-slate-200 transition hover:ring-2 hover:ring-blue-400"
                       title="Buka foto nota ini">
                        <img src="{{ Storage::url($path) }}" alt="Foto nota {{ $nota['nomor'] }}"
                             loading="lazy"
                             class="h-full w-full object-cover transition duration-200 group-hover/foto:scale-105">
                        <span class="absolute inset-x-0 bottom-0 bg-slate-900/65 py-0.5 text-center text-[9px] font-bold uppercase tracking-wide text-white">
                            Foto
                        </span>
                    </a>
                @endforeach

                <a href="{{ Storage::url($fotoNota->first()) }}" target="_blank"
                   class="ml-auto inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-2 text-[11px] font-bold text-blue-600 shadow-sm ring-1 ring-blue-100 transition hover:bg-blue-50">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/>
                    </svg>
                    {{ $fotoNota->count() > 1 ? 'Lihat ' . $fotoNota->count() . ' Foto' : 'Lihat Foto' }}
                </a>
            </div>
        @endif

        <table class="table-item min-w-[760px]">
            <thead>
                <tr>
                    <th class="td-no">#</th>
                    <th>Warna / Unit</th>
                    @if ($adaPlat)
                        <th>No. Plat</th>
                    @endif
                    <th>Bahan</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Diskon</th>
                    <th class="text-right">Total Item</th>
                    <th class="text-right">Harga/CC</th>
                    @if ($bisaAksi)
                        <th class="text-center">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($nota['items'] as $item)
                    <tr>
                        <td class="td-no">{{ $loop->iteration }}</td>
                        <td class="font-semibold text-slate-800">{{ $item->kode_warna_unit }}</td>
                        @if ($adaPlat)
                            <td>{{ $item->no_plat }}</td>
                        @endif
                        <td>
                            <span class="font-semibold text-slate-800">{{ $item->rincian_bahan }}</span>
                            @if ($item->nomor_urut)
                                <span class="text-[11px] text-slate-400">&middot; item #{{ $item->nomor_urut }}</span>
                            @endif
                        </td>
                        <td class="text-right"><span class="angka-kecil text-slate-700">{{ number_format($item->qty_cc) }}</span></td>
                        <td class="text-right"><span class="angka-kecil">{{ $rupiah($item->harga_nota) }}</span></td>
                        @php $nominalDiskon = (float) $item->nominal_diskon; @endphp
                        {{-- Oplosan memang tanpa diskon, tapi tetap ditulis Rp 0
                             supaya formatnya sama dengan riwayat order. --}}
                        <td class="text-right">
                            <span class="angka-kecil {{ $nominalDiskon > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ Rupiah::formatRingkas($nominalDiskon) }}
                            </span>
                        </td>
                        <td class="text-right">
                            <span class="angka-sel">{{ $rupiah((float) $item->harga_nota - $nominalDiskon) }}</span>
                        </td>
                        <td class="text-right">
                            <span class="angka-kecil">
                                {{ $item->qty_cc > 0 ? $rupiah((float) $item->harga_nota / $item->qty_cc) : '-' }}
                            </span>
                        </td>
                        @if ($bisaAksi)
                            <td>
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('laporan-oplosan.edit', $item) }}"
                                       class="btn-act bg-amber-50 text-amber-700 hover:bg-amber-100" title="Edit item ini">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('laporan-oplosan.destroy', $item) }}" method="POST"
                                          onsubmit="return confirm('Yakin hapus item ini dari nota? Data yang dihapus tidak bisa dikembalikan.')">
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
                    {{-- Rekap nota: harga nota - diskon = grand total nota. --}}
                    <tr>
                        <td colspan="{{ 6 + ($adaPlat ? 1 : 0) }}"
                            class="angka-label text-right">
                            Total sebelum diskon
                        </td>
                        <td class="text-right">
                            <span class="angka-kecil text-slate-700">
                                {{ $rupiah($nota['items']->sum(fn ($item) => (float) $item->harga_nota)) }}
                            </span>
                        </td>
                        <td></td>
                        @if ($bisaAksi)
                            <td></td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="{{ 6 + ($adaPlat ? 1 : 0) }}"
                            class="angka-label text-right">
                            Diskon
                        </td>
                        <td class="text-right">
                            <span class="angka-kecil text-emerald-600">&minus; {{ $rupiah($diskonNota) }}</span>
                        </td>
                        <td></td>
                        @if ($bisaAksi)
                            <td></td>
                        @endif
                    </tr>
                @endif
                <tr>
                    <td colspan="{{ 6 + ($adaPlat ? 1 : 0) }}" class="angka-label border-t-2 border-slate-200 text-right">
                        Grand Total Nota
                    </td>
                    <td class="border-t-2 border-slate-200 text-right"><span class="angka-total">{{ $rupiah($nota['total']) }}</span></td>
                    @if ($diskonNota > 0)
                        <td class="border-t-2 border-slate-200"></td>
                    @endif
                    @if ($bisaAksi)
                        <td class="border-t-2 border-slate-200"></td>
                    @endif
                </tr>
            </tfoot>
        </table>
        </div>
    </details>
</div>
