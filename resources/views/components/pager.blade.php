{{--
    Navigasi halaman untuk daftar panjang.

    Dipakai di halaman index laporan harian & dashboard supaya daftar yang
    isinya banyak tetap pendek - hanya beberapa baris yang tampil, sisanya
    dibuka lewat tombol halaman.

    Param:
      $paginator  hasil paginate() / LengthAwarePaginator
      $anchor     (opsional) id elemen yang di-scroll setelah pindah halaman
      $ringkas    (opsional) true = tanpa baris "Menampilkan x-y dari z"
--}}
@if ($paginator->hasPages())
    <div @if (!empty($anchor)) id="{{ $anchor }}" @endif
         class="flex flex-col gap-3 border-t border-slate-200/60 px-4 py-3.5 sm:flex-row sm:items-center sm:justify-between sm:px-5">
        @unless (!empty($ringkas))
            <p class="text-[11px] text-slate-500">
                Menampilkan
                <span class="font-bold text-slate-700">{{ number_format($paginator->firstItem()) }}</span>&ndash;<span class="font-bold text-slate-700">{{ number_format($paginator->lastItem()) }}</span>
                dari <span class="font-bold text-slate-700">{{ number_format($paginator->total()) }}</span>
            </p>
        @endunless

        <nav class="flex items-center gap-1.5" aria-label="Navigasi halaman">
            {{-- Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="pager-btn pager-mati" aria-disabled="true" title="Halaman sebelumnya">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pager-btn" rel="prev" title="Halaman sebelumnya">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            @endif

            {{-- Nomor halaman. Yang jauh diringkas jadi elipsis supaya
                 tombolnya tidak memanjang kalau datanya ratusan baris. --}}
            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $halaman => $url)
                @php
                    $kini = $paginator->currentPage();
                    $dekat = abs($halaman - $kini) <= 1;
                    $ujung = $halaman === 1 || $halaman === $paginator->lastPage();
                @endphp

                @if ($dekat || $ujung)
                    @if ($halaman === $kini)
                        <span class="pager-btn pager-aktif" aria-current="page">{{ $halaman }}</span>
                    @else
                        <a href="{{ $url }}" class="pager-btn">{{ $halaman }}</a>
                    @endif
                @elseif ($halaman === 2 || $halaman === $paginator->lastPage() - 1)
                    <span class="px-1 text-xs font-bold text-slate-400">&hellip;</span>
                @endif
            @endforeach

            {{-- Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pager-btn" rel="next" title="Halaman berikutnya">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span class="pager-btn pager-mati" aria-disabled="true" title="Halaman berikutnya">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif
        </nav>
    </div>
@endif
