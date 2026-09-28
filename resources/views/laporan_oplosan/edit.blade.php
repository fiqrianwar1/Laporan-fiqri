@extends('layouts.app')

@section('title', 'Edit Laporan Oplosan')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    {{-- Header: bentuknya sama dengan halaman Tambah, hanya beda warna badge. --}}
    <div class="page-head">
        <div>
            <div class="pill pill-amber mb-2">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"></span>
                Mode Edit
            </div>
            <h1 class="page-title">Edit Laporan Oplosan</h1>
            <p class="page-sub">Perbarui data oplosan yang sudah tersimpan sebelumnya.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('laporan-oplosan.index') }}" class="btn btn-outline btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if ($saudaraNota->isNotEmpty())
        {{-- Pengingat item lain di nota yang sama: supaya nomor urut tidak tertukar. --}}
        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-indigo-700">
                Nota ini berisi {{ $saudaraNota->count() + 1 }} item
            </p>
            <ul class="mt-2 space-y-1 text-xs text-slate-600">
                @foreach ($saudaraNota as $lain)
                    <li class="flex items-center justify-between gap-3">
                        <span class="truncate">
                            <span class="font-bold text-slate-500">#{{ $lain->nomor_urut ?? '-' }}</span>
                            {{ $lain->rincian_bahan }}
                        </span>
                        <span class="shrink-0 font-semibold text-slate-700">{{ number_format($lain->qty_cc) }} CC</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('laporan-oplosan.update', $laporan) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Identitas nota --}}
        <div class="form-card">
            <div class="form-card-head">
                <div class="stat-icon bg-gradient-to-br from-blue-500 to-blue-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 3v2m6-2v2M4 8h16M6 6h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="card-title">Identitas Nota</h2>
                    <p class="card-sub">Item lain dengan nomor nota yang sama akan ikut jadi satu nota.</p>
                </div>
            </div>

            <div class="form-card-body">
                <div class="sm:col-span-2">
                    <label for="cabang_area" class="label">Cabang / Area</label>
                    <select id="cabang_area" name="cabang_area" class="input">
                        @foreach (\App\Support\Cabang::daftar() as $cabang)
                            <option value="{{ $cabang }}" {{ old('cabang_area', $laporan->cabang_area) === $cabang ? 'selected' : '' }}>
                                {{ $cabang }}
                            </option>
                        @endforeach
                    </select>
                    @error('cabang_area') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tanggal" class="label">Tanggal</label>
                    <input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal', $laporan->tanggal->format('Y-m-d')) }}"
                           class="input">
                    @error('tanggal') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_bukti_nota" class="label">No. Bukti Nota</label>
                    <input id="no_bukti_nota" type="text" name="no_bukti_nota" value="{{ old('no_bukti_nota', $laporan->no_bukti_nota) }}"
                           class="input">
                    <p class="mt-1.5 text-xs text-slate-400">Pakai nomor yang sama untuk semua item dalam satu nota.</p>
                    @error('no_bukti_nota') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Detail oplosan --}}
        <div class="form-card">
            <div class="form-card-head">
                <div class="stat-icon bg-gradient-to-br from-emerald-400 to-emerald-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="card-title">Detail Oplosan</h2>
                    <p class="card-sub">Warna/unit yang dioplos, bahan yang dipakai, qty, dan harganya.</p>
                </div>
            </div>

            <div class="form-card-body">
                <div>
                    <label for="no_plat" class="label">No. Plat</label>
                    <input id="no_plat" type="text" name="no_plat" value="{{ old('no_plat', $laporan->no_plat) }}" class="input">
                    @error('no_plat') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="kode_warna_unit" class="label">Kode Warna / Unit</label>
                    <input id="kode_warna_unit" type="text" name="kode_warna_unit" value="{{ old('kode_warna_unit', $laporan->kode_warna_unit) }}"
                           class="input">
                    @error('kode_warna_unit') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="rincian_bahan" class="label">Rincian Bahan / Oplosan</label>
                    <input id="rincian_bahan" type="text" name="rincian_bahan" value="{{ old('rincian_bahan', $laporan->rincian_bahan) }}"
                           class="input">
                    @error('rincian_bahan') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="qty_cc" class="label">Qty (CC/LTR)</label>
                    <input id="qty_cc" type="number" name="qty_cc" value="{{ old('qty_cc', $laporan->qty_cc) }}" class="input">
                    @error('qty_cc') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div x-data="rupiahInput({{ (float) old('harga_nota', $laporan->harga_nota) }})">
                    <label for="harga_nota_tampil" class="label">Harga Nota (Rp)</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-sm font-medium text-slate-400">Rp</span>
                        <input id="harga_nota_tampil" type="text" inputmode="numeric" autocomplete="off" x-model="tampil"
                               class="input pl-10" placeholder="0">
                        <input type="hidden" name="harga_nota" :value="nilai">
                    </div>
                    <p class="mt-1.5 text-xs text-slate-400">Ketik angka saja, pemisah ribuan otomatis ditambahkan.</p>
                    @error('harga_nota') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nomor_urut" class="label">Nomor Urut Item</label>
                    <input id="nomor_urut" type="number" min="1" step="1" name="nomor_urut" value="{{ old('nomor_urut', $laporan->nomor_urut) }}"
                           class="input">
                    <p class="mt-1.5 text-xs text-slate-400">Urutan bahan di dalam nota ini.</p>
                    @error('nomor_urut') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Bukti nota --}}
        <div class="form-card">
            <div class="form-card-head">
                <div class="stat-icon bg-gradient-to-br from-violet-500 to-fuchsia-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16.5V5a2 2 0 0 1 2-2h8.586A2 2 0 0 1 16 3.586L20.414 8A2 2 0 0 1 21 9.414V19a2 2 0 0 1-2 2H8m-4-4 4 4m0-4v4"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="card-title">Foto Nota</h2>
                    <p class="card-sub">Kosongkan bila tidak ingin mengganti foto yang sudah tersimpan.</p>
                </div>
            </div>

            <div class="p-4 sm:p-5">
                @if ($laporan->foto_nota)
                    <div class="mb-4 flex flex-col gap-3 rounded-xl border border-blue-100 bg-blue-50/50 p-3.5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-blue-100 bg-white text-blue-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">Foto saat ini</p>
                                <p class="text-xs text-slate-500">Upload file baru untuk menggantinya.</p>
                            </div>
                        </div>
                        <a href="{{ Storage::url($laporan->foto_nota) }}" target="_blank"
                           class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-3.5 py-2 text-xs font-semibold text-blue-600 shadow-sm ring-1 ring-blue-100 transition hover:bg-blue-50">
                            Lihat Foto
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4m-3-7 5 5m0 0V4m0 5h-5"/>
                            </svg>
                        </a>
                    </div>
                @endif

                <label for="foto_nota"
                       class="group flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300/80 bg-white/50 px-6 py-8 text-center transition hover:border-blue-400 hover:bg-white">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 transition-colors group-hover:text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0L8 8m4-4 4 4M5 20h14"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-700">Pilih foto nota</p>
                    <p class="mt-1 text-xs text-slate-400">JPG, JPEG, PNG atau format gambar lainnya</p>
                    <input id="foto_nota" type="file" name="foto_nota" accept="image/*" class="hidden">
                </label>
                <p id="file-name" class="mt-2 hidden text-xs font-bold text-blue-600"></p>
                @error('foto_nota') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Aksi --}}
        <div class="form-actions">
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('laporan-oplosan.index') }}" class="btn btn-outline flex-1 sm:flex-none">Batal</a>
                <button type="submit" class="btn btn-primary flex-1 sm:flex-none">Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>

<script>
// Tampilkan nama file yang dipilih supaya pengguna yakin fotonya masuk.
document.getElementById('foto_nota')?.addEventListener('change', function () {
    const fileName = document.getElementById('file-name');
    if (!fileName) return;

    if (this.files?.length) {
        fileName.textContent = `File dipilih: ${this.files[0].name}`;
        fileName.classList.remove('hidden');
    } else {
        fileName.classList.add('hidden');
    }
});
</script>
@endsection
