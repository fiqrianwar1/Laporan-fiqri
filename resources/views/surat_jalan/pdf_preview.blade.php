@extends('layouts.app')

@section('title', 'Preview PDF Surat Jalan')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-700 backdrop-blur-md border-blue-500/20">
                <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                Preview Dokumen
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Surat Jalan</h1>
            <p class="mt-1 text-sm text-slate-500">
                Dokumen ditampilkan langsung di bawah. Cek dulu isinya, baru simpan atau print.
                @if ($namaBulan)
                    <span class="font-semibold text-slate-700">Periode: {{ $namaBulan }} {{ $tahun }}</span>
                @elseif ($tahun)
                    <span class="font-semibold text-slate-700">Semua bulan {{ $tahun }}</span>
                @endif
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('surat-jalan.index', request()->query()) }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl border-slate-200/60 bg-white/70 backdrop-blur-md px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-white hover:shadow-md transition-all duration-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0 7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>

            <button type="button" onclick="printPdf()"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border-slate-900 bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-slate-900/20 hover:bg-slate-800 hover:-translate-y-0.5 transition-all duration-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V4h12v5M6 18H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-1M6 14h12v6H6v-6Z"/>
                </svg>
                Print
            </button>

            <a href="{{ $downloadUrl }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-0.5 transition-all duration-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a2 2 0 0 1 1.414.586l5.414 5.414A2 2 0 0 1 19 10.414V19a2 2 0 0 1-2 2Z"/>
                </svg>
                Download PDF
            </a>
        </div>
    </div>

    {{-- Filter periode --}}
    @php use App\Support\Cabang; @endphp
    <div class="rounded-2xl border-white/50 bg-white/70 p-4 shadow-sm backdrop-blur-xl sm:p-5">
        <form method="GET" action="{{ route('surat-jalan.preview') }}"
              class="grid grid-cols-1 items-end gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="bulan" class="label">Bulan</label>
                <div class="relative">
                    <select id="bulan" name="bulan" class="input appearance-none pr-10">
                        <option value="">Semua Bulan</option>
                        @foreach (['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $val => $label)
                            <option value="{{ $val }}" {{ (string) request('bulan') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <div>
                <label for="tahun" class="label">Tahun</label>
                <input id="tahun" type="number" name="tahun" value="{{ request('tahun') }}" min="2000" max="2100" placeholder="Semua" class="input">
            </div>

            <div>
                <label for="cabang" class="label">Cabang / Area</label>
                <div class="relative">
                    <select id="cabang" name="cabang" class="input appearance-none pr-10">
                        <option value="">Semua Cabang</option>
                        @foreach (Cabang::daftar() as $c)
                            <option value="{{ $c }}" {{ request('cabang') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="btn btn-dark h-[42px] flex-1">Terapkan</button>
                @if (request('bulan') || request('tahun') || request('cabang'))
                    <a href="{{ route('surat-jalan.preview') }}" class="btn btn-ghost h-[42px] shrink-0">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Ringkasan singkat --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border-white/50 bg-white/60 backdrop-blur-xl p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Surat</p>
            <p class="mt-1.5 text-2xl font-bold text-slate-900">{{ number_format($surat->count()) }}</p>
        </div>
        <div class="rounded-2xl border-white/50 bg-white/60 backdrop-blur-xl p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Barang</p>
            <p class="mt-1.5 text-2xl font-bold text-slate-900">{{ number_format($totalBaris) }}</p>
        </div>
        <div class="rounded-2xl border-white/50 bg-white/60 backdrop-blur-xl p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Jumlah Dikirim</p>
            <p class="mt-1.5 text-2xl font-bold text-emerald-600">{{ number_format($totalJumlah) }}</p>
        </div>
    </div>

    {{-- Preview PDF --}}
    <div class="overflow-hidden rounded-2xl border-white/50 bg-white/70 backdrop-blur-xl shadow-sm">
        <div class="flex flex-col gap-2 border-b border-slate-200/60 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Preview Dokumen</h2>
                    <p class="text-xs text-slate-500">{{ $namaFile }}</p>
                </div>
            </div>
            <a href="{{ $streamUrl }}" target="_blank"
               class="inline-flex w-fit items-center gap-2 rounded-full bg-slate-800 px-4 py-1.5 text-xs font-semibold text-white shadow-sm shadow-slate-800/20 hover:bg-slate-900 transition">
                Buka di Tab Baru
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        </div>

        <div class="relative bg-slate-200/50 p-4 sm:p-6">
            <div id="pdf-loader" class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-3 bg-slate-100/80 backdrop-blur-sm">
                <svg class="h-8 w-8 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p class="text-sm font-medium text-slate-600">Menyiapkan preview dokumen...</p>
            </div>

            <div id="pdf-fallback" class="hidden flex-col items-center justify-center gap-4 rounded-xl bg-white p-10 text-center shadow-inner">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Browser ini tidak menampilkan PDF langsung</h3>
                    <p class="mt-1 text-sm text-slate-500">Dokumennya tetap ada kok &mdash; buka di tab baru, atau langsung download.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ $streamUrl }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition">
                        Buka di Tab Baru
                    </a>
                    <a href="{{ $downloadUrl }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:shadow-lg transition">
                        Download PDF
                    </a>
                </div>
            </div>

            {{-- Toolbar viewer sengaja dibiarkan tampil: di dalam iframe browser
                 tidak menyediakan kontrol zoom, jadi pengguna perlu toolbar untuk
                 memperbesar atau menggeser halaman. --}}
            <iframe id="pdf-frame"
                    src="{{ $streamUrl }}#view=FitH"
                    title="Preview PDF Surat Jalan"
                    class="block h-[70vh] min-h-[460px] w-full rounded-xl bg-white shadow-inner sm:h-[80vh] sm:min-h-[620px]"></iframe>
        </div>
    </div>
</div>

<script>
    const pdfFrame = document.getElementById('pdf-frame');
    const pdfLoader = document.getElementById('pdf-loader');
    const hideLoader = () => pdfLoader.classList.add('hidden');
    const pdfFallback = document.getElementById('pdf-fallback');

    if (pdfFrame) {
        pdfFrame.addEventListener('load', hideLoader);

        if (pdfFrame.complete) {
            hideLoader();
        } else {
            fetch(pdfFrame.getAttribute('src').split('#')[0], { method: 'GET', credentials: 'same-origin' })
                .then(hideLoader)
                .catch(hideLoader);
        }

        setTimeout(hideLoader, 5000);
    }

    function printPdf() {
        try {
            pdfFrame.contentWindow.focus();
            pdfFrame.contentWindow.print();
        } catch (e) {
            window.open(pdfFrame.src, '_blank');
        }
    }

    const supportsPdfViewer = navigator.pdfViewerEnabled !== false;

    if (!supportsPdfViewer && pdfFallback) {
        pdfFrame.classList.add('hidden');
        pdfFallback.classList.remove('hidden');
        pdfFallback.classList.add('flex');
        if (pdfLoader) pdfLoader.classList.add('hidden');
    }
</script>
@endsection
