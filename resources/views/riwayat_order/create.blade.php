@extends('layouts.app')

@section('title', 'Tambah Riwayat Order')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    {{-- Header: satu bentuk dengan halaman Tambah/Edit lainnya. --}}
    <div class="page-head">
        <div>
            <div class="pill pill-blue mb-2">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-600"></span>
                Tambah Data
            </div>
            <h1 class="page-title">Tambah Riwayat Order</h1>
            <p class="page-sub">Catat pembelian bahan dan consumable baru.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('riwayat-order.index') }}" class="btn btn-outline btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('riwayat-order.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- Informasi order --}}
        <div class="form-card">
            <div class="form-card-head">
                <div class="stat-icon bg-gradient-to-br from-blue-500 to-blue-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a3 3 0 0 1 6 0M9 5h6"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="card-title">Informasi Order</h2>
                    <p class="card-sub">Satu nota boleh berisi banyak item. Isi No. Bukti Faktur yang sama untuk tiap barang, lalu beri nomor urutnya.</p>
                </div>
            </div>

            <div class="form-card-body">
                <div class="sm:col-span-2">
                    <label for="cabang_area" class="label">Cabang / Area <span class="text-rose-500">*</span></label>
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
                    <label for="tanggal" class="label">Tanggal <span class="text-rose-500">*</span></label>
                    <input id="tanggal" type="date" name="tanggal" value="{{ old('tanggal') }}" class="input">
                    @error('tanggal') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_bukti_faktur" class="label">No. Bukti Faktur <span class="text-rose-500">*</span></label>
                    <input id="no_bukti_faktur" type="text" name="no_bukti_faktur" value="{{ old('no_bukti_faktur') }}"
                           placeholder="Contoh: WPJ.260905001" class="input">
                    <p class="mt-1.5 text-xs text-slate-400">Pakai nomor yang sama untuk semua item dalam satu nota.</p>
                    @error('no_bukti_faktur') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nomor_urut" class="label">Nomor Urut Item <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input id="nomor_urut" type="number" min="1" step="1" name="nomor_urut" value="{{ old('nomor_urut') }}"
                           placeholder="1" class="input">
                    <p class="mt-1.5 text-xs text-slate-400">Item ke-berapa di nota ini. Kosongkan agar otomatis di urutan terakhir.</p>
                    @error('nomor_urut') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Detail barang --}}
        <div class="form-card">
            <div class="form-card-head">
                <div class="stat-icon bg-gradient-to-br from-emerald-400 to-emerald-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Zm-8 0V4m0 0 2 2m-2-2-2 2"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="card-title">Detail Barang</h2>
                    <p class="card-sub">Masukkan material yang dibeli beserta kuantitasnya.</p>
                </div>
            </div>

            <div class="form-card-body">
                <div>
                    <label for="kode_barang" class="label">Kode Barang <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input id="kode_barang" type="text" name="kode_barang" value="{{ old('kode_barang') }}"
                           placeholder="Contoh: MAT-001" class="input">
                    @error('kode_barang') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nama_barang" class="label">Nama Barang / Material <span class="text-rose-500">*</span></label>
                    <input id="nama_barang" type="text" name="nama_barang" value="{{ old('nama_barang') }}"
                           placeholder="Contoh: Thinner, Primer, Amplas" class="input">
                    @error('nama_barang') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="qty" class="label">Qty <span class="text-rose-500">*</span></label>
                    <input id="qty" type="number" min="0" step="1" name="qty" value="{{ old('qty') }}"
                           placeholder="0" class="input">
                    @error('qty') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="satuan" class="label">Satuan <span class="text-rose-500">*</span></label>
                    <input id="satuan" type="text" name="satuan" value="{{ old('satuan') }}"
                           placeholder="ROL / GLN / PCS / LTR / TIN" class="input uppercase">
                    <p class="mt-1.5 text-xs text-slate-400">Gunakan satuan yang sesuai dengan barang.</p>
                    @error('satuan') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Harga --}}
        <div class="form-card">
            <div class="form-card-head">
                <div class="stat-icon bg-gradient-to-br from-amber-400 to-orange-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v-2"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="card-title">Harga &amp; Diskon</h2>
                    <p class="card-sub">Atur harga satuan dan potongan pembelian.</p>
                </div>
            </div>

            <div class="form-card-body"
                 x-data="hitungDiskon({{ (float) old('harga_satuan', 0) }}, {{ (int) old('qty', 0) }}, {{ (float) old('diskon_persen', 0) }})">
                <div x-data="rupiahInput({{ (float) old('harga_satuan', 0) }})"
                     x-effect="$dispatch('harga-diubah', { nilai })">
                    <label for="harga_satuan" class="label">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-sm font-medium text-slate-400">Rp</span>
                        <input id="harga_satuan" type="text" inputmode="numeric" autocomplete="off" x-model="tampil"
                               placeholder="0" class="input pl-10">
                        <input type="hidden" name="harga_satuan" :value="nilai">
                    </div>
                    <p class="mt-1.5 text-xs text-slate-400">Ketik angka saja, pemisah ribuan otomatis ditambahkan.</p>
                    @error('harga_satuan') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="diskon_persen" class="label">Diskon <span class="font-normal text-slate-400">(%)</span></label>
                    <div class="relative">
                        <input id="diskon_persen" type="number" min="0" max="100" step="0.01" name="diskon_persen"
                               value="{{ old('diskon_persen', 0) }}" x-model.number="diskon"
                               placeholder="0" class="input pr-10">
                        <span class="pointer-events-none absolute inset-y-0 right-3.5 flex items-center text-sm font-medium text-slate-400">%</span>
                    </div>
                    @error('diskon_persen') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Ringkasan hitungan: nominal diskon & total setelah potongan,
                     supaya nominalnya sudah ketahuan sebelum disimpan. --}}
                <div class="sm:col-span-2 rounded-xl bg-slate-50/80 px-4 py-3 text-xs text-slate-600 ring-1 ring-slate-200/70">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span>Total sebelum diskon: <span class="angka-kecil font-bold text-slate-800" x-text="rupiah(totalKotor)"></span></span>
                        <span>Nominal diskon: <span class="angka-kecil font-bold text-emerald-600" x-text="rupiah(nominalDiskon)"></span></span>
                        <span>Total setelah diskon: <span class="angka-kecil font-bold text-slate-900" x-text="rupiah(totalAkhir)"></span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bukti --}}
        <div class="form-card">
            <div class="form-card-head">
                <div class="stat-icon bg-gradient-to-br from-violet-500 to-fuchsia-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16.5V5a2 2 0 0 1 2-2h8.586A2 2 0 0 1 16 3.586L20.414 8A2 2 0 0 1 21 9.414V19a2 2 0 0 1-2 2H8m-4-4 4 4m0-4v4"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="card-title">Bukti Faktur</h2>
                    <p class="card-sub">Kelola foto faktur pembelian untuk dokumentasi.</p>
                </div>
            </div>

            <div class="p-4 sm:p-5">
                <label for="foto_faktur"
                       class="group flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300/80 bg-white/50 px-6 py-8 text-center transition hover:border-blue-400 hover:bg-white">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 transition-colors group-hover:text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0L8 8m4-4 4 4M5 20h14"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-700">Pilih foto faktur</p>
                    <p class="mt-1 text-xs text-slate-400">JPG, JPEG, PNG atau format gambar lainnya</p>
                    <input id="foto_faktur" type="file" name="foto_faktur" accept="image/*" class="hidden">
                </label>
                <p id="file-name" class="mt-2 hidden text-xs font-bold text-blue-600"></p>
                <p class="mt-2 text-xs text-slate-400">Foto faktur bersifat opsional dan dapat membantu dokumentasi pembelian.</p>
                @error('foto_faktur') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Aksi --}}
        <div class="form-actions">
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="hidden text-xs font-medium text-slate-500 sm:block">
                    Pastikan data yang diisi sudah benar sebelum disimpan.
                </p>
                <div class="flex w-full gap-3 sm:w-auto">
                    <a href="{{ route('riwayat-order.index') }}" class="btn btn-outline flex-1 sm:flex-none">Batal</a>
                    <button type="submit" class="btn btn-primary flex-1 sm:flex-none">Simpan Order</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Tampilkan nama file yang dipilih supaya pengguna yakin fotonya masuk.
document.getElementById('foto_faktur')?.addEventListener('change', function () {
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