@extends('layouts.app')

@section('title', 'Tambah Laporan Oplosan')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    {{-- Header: judul halaman + tombol kembali. Bentuknya sama dengan
         halaman Edit, jadi pindah antara keduanya tidak terasa berbeda. --}}
    <div class="page-head">
        <div>
            <div class="pill pill-blue mb-2">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-600"></span>
                Tambah Data
            </div>
            <h1 class="page-title">Tambah Laporan Baru</h1>
            <p class="page-sub">Lengkapi form di bawah ini untuk mencatat data oplosan baru.</p>
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

    {{-- Form --}}
    <form action="{{ route('laporan-oplosan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

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
                    <p class="card-sub">Cabang, tanggal, dan nomor bukti nota. Nomor yang sama akan digabung jadi satu nota.</p>
                </div>
            </div>

            <div class="form-card-body">
                <div class="sm:col-span-2">
                    <label for="cabang_area" class="label">Cabang / Area</label>
                    <select id="cabang_area" name="cabang_area" class="input">
                        @foreach (\App\Support\Cabang::daftar() as $cabang)
                            <option value="{{ $cabang }}" {{ old('cabang_area', \App\Support\Cabang::default()) === $cabang ? 'selected' : '' }}>
                                {{ $cabang }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1.5 text-xs text-slate-400">Nota ini masuk hitungan cabang mana.</p>
                    @error('cabang_area') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tanggal" class="label">Tanggal</label>
                    <input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal') }}" class="input">
                    @error('tanggal') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_bukti_nota" class="label">No. Bukti Nota</label>
                    <input id="no_bukti_nota" type="text" name="no_bukti_nota" value="{{ old('no_bukti_nota') }}" placeholder="Contoh: WPJ.260905001"
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
                    <input id="no_plat" type="text" name="no_plat" value="{{ old('no_plat') }}" placeholder="Contoh: DA 1234 XY" class="input">
                    @error('no_plat') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="kode_warna_unit" class="label">Kode Warna / Unit</label>
                    <input id="kode_warna_unit" type="text" name="kode_warna_unit" value="{{ old('kode_warna_unit') }}" placeholder="Contoh: Super White 040"
                           class="input">
                    @error('kode_warna_unit') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="rincian_bahan" class="label">Rincian Bahan / Oplosan</label>
                    <input id="rincian_bahan" type="text" name="rincian_bahan" value="{{ old('rincian_bahan') }}" placeholder="Contoh: Thinner, Cat Dasar, Clear"
                           class="input">
                    @error('rincian_bahan') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="qty_cc" class="label">Qty (CC/LTR)</label>
                    <input id="qty_cc" type="number" name="qty_cc" value="{{ old('qty_cc') }}" placeholder="0" class="input">
                    @error('qty_cc') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div x-data="rupiahInput({{ (float) old('harga_nota', 0) }})">
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
                    <label for="nomor_urut" class="label">Nomor Urut Item <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input id="nomor_urut" type="number" min="1" step="1" name="nomor_urut" value="{{ old('nomor_urut') }}" placeholder="1" class="input">
                    <p class="mt-1.5 text-xs text-slate-400">Item ke-berapa di nota ini. Kosongkan agar otomatis di urutan terakhir.</p>
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
                    <p class="card-sub">Opsional, tapi sangat membantu saat data perlu dicek ulang.</p>
                </div>
            </div>

            <div class="p-4 sm:p-5">
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
                <button type="submit" class="btn btn-primary flex-1 sm:flex-none">Simpan Laporan</button>
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
