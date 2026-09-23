@extends('layouts.app')

@section('title', 'Tambah Riwayat Order')

@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    {{-- Page header --}}
    <div class="mb-5 flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-500/20 backdrop-blur-md">
                <a href="{{ route('riwayat-order.index') }}" class="hover:text-blue-800 transition">Riwayat Order</a>
                <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/>
                </svg>
                <span class="font-bold">Tambah</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Tambah Riwayat Order</h1>
            <p class="mt-1 text-sm text-slate-500">Catat pembelian bahan dan consumable baru.</p>
        </div>

        <a href="{{ route('riwayat-order.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200/60 bg-white/70 backdrop-blur-md px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-white hover:shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('riwayat-order.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
 
        {{-- Informasi order --}}
        <div class="overflow-hidden rounded-2xl border border-white/50 bg-white/60 backdrop-blur-xl shadow-sm relative group hover:shadow-md transition-all duration-300">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-blue-500/10 blur-2xl transition-all group-hover:bg-blue-500/20"></div>
            
            <div class="border-b border-slate-200/50 bg-white/40 px-5 py-4 sm:px-6 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a3 3 0 0 1 6 0M9 5h6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900">Informasi Order</h2>
                        <p class="text-xs text-slate-500">Satu nota boleh berisi banyak item. Isi No. Bukti Faktur yang sama untuk tiap barang, lalu beri nomor urutnya.</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-6 relative z-10">
                <div class="sm:col-span-2">
                    <label for="cabang_area" class="label">Cabang / Area <span class="text-rose-500">*</span></label>
                    <select id="cabang_area" name="cabang_area" class="input appearance-none py-3">
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
                    <input id="tanggal" type="date" name="tanggal"
                           value="{{ old('tanggal') }}"
                           class="input py-3">
                    @error('tanggal') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_bukti_faktur" class="label">No. Bukti Faktur <span class="text-rose-500">*</span></label>
                    <input id="no_bukti_faktur" type="text" name="no_bukti_faktur"
                           value="{{ old('no_bukti_faktur') }}"
                           placeholder="Contoh: WPJ.260905001"
                           class="input py-3">
                    <p class="mt-1.5 text-xs text-slate-400">Pakai nomor yang sama untuk semua item dalam satu nota.</p>
                    @error('no_bukti_faktur') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nomor_urut" class="label">Nomor Urut Item <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input id="nomor_urut" type="number" min="1" step="1" name="nomor_urut"
                           value="{{ old('nomor_urut') }}"
                           placeholder="1"
                           class="input py-3">
                    <p class="mt-1.5 text-xs text-slate-400">Item ke-berapa di nota ini. Kosongkan agar otomatis di urutan terakhir.</p>
                    @error('nomor_urut') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Detail barang --}}
        <div class="overflow-hidden rounded-2xl border border-white/50 bg-white/60 backdrop-blur-xl shadow-sm relative group hover:shadow-md transition-all duration-300">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
            
            <div class="border-b border-slate-200/50 bg-white/40 px-5 py-4 sm:px-6 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-lg shadow-emerald-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Zm-8 0V4m0 0 2 2m-2-2-2 2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900">Detail Barang</h2>
                        <p class="text-xs text-slate-500">Masukkan material yang dibeli beserta kuantitasnya.</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-6 relative z-10">
                <div>
                    <label for="kode_barang" class="label">Kode Barang <span class="font-normal text-slate-400">(opsional)</span></label>
                    <input id="kode_barang" type="text" name="kode_barang"
                           value="{{ old('kode_barang') }}"
                           placeholder="Contoh: MAT-001"
                           class="input py-3">
                    @error('kode_barang') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nama_barang" class="label">Nama Barang / Material <span class="text-rose-500">*</span></label>
                    <input id="nama_barang" type="text" name="nama_barang"
                           value="{{ old('nama_barang') }}"
                           placeholder="Contoh: Thinner, Primer, Amplas"
                           class="input py-3">
                    @error('nama_barang') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="qty" class="label">Qty <span class="text-rose-500">*</span></label>
                    <input id="qty" type="number" min="0" step="1" name="qty"
                           value="{{ old('qty') }}"
                           placeholder="0"
                           class="input py-3">
                    @error('qty') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="satuan" class="label">Satuan <span class="text-rose-500">*</span></label>
                    <input id="satuan" type="text" name="satuan"
                           value="{{ old('satuan') }}"
                           placeholder="ROL / GLN / PCS / LTR / TIN"
                           class="input py-3">
                    <p class="mt-1.5 text-xs text-slate-400">Gunakan satuan yang sesuai dengan barang.</p>
                    @error('satuan') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Harga --}}
        <div class="overflow-hidden rounded-2xl border border-white/50 bg-white/60 backdrop-blur-xl shadow-sm relative group hover:shadow-md transition-all duration-300">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all"></div>
            
            <div class="border-b border-slate-200/50 bg-white/40 px-5 py-4 sm:px-6 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-lg shadow-amber-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v-2m0-8v0m0 10v0"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900">Harga & Diskon</h2>
                        <p class="text-xs text-slate-500">Atur harga satuan dan potongan pembelian.</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-6 relative z-10">
                <div>
                    <label for="harga_satuan" class="label">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-sm font-medium text-slate-400">Rp</span>
                        <input id="harga_satuan" type="number" min="0" step="0.01" name="harga_satuan"
                               value="{{ old('harga_satuan') }}"
                               placeholder="0"
                               class="input py-3">
                    </div>
                    @error('harga_satuan') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="diskon_persen" class="label">Diskon <span class="font-normal text-slate-400">(%)</span></label>
                    <div class="relative">
                        <input id="diskon_persen" type="number" min="0" max="100" step="0.01" name="diskon_persen"
                               value="{{ old('diskon_persen', 0) }}"
                               placeholder="0"
                               class="input py-3">
                        <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-slate-400">%</span>
                    </div>
                    @error('diskon_persen') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Bukti --}}
        <div class="overflow-hidden rounded-2xl border border-white/50 bg-white/60 backdrop-blur-xl shadow-sm relative group hover:shadow-md transition-all duration-300">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-violet-500/10 rounded-full blur-2xl group-hover:bg-violet-500/20 transition-all"></div>
            
            <div class="border-b border-slate-200/50 bg-white/40 px-5 py-4 sm:px-6 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-500 text-white shadow-lg shadow-violet-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16.5V5a2 2 0 0 1 2-2h8.586A2 2 0 0 1 16 3.586L20.414 8A2 2 0 0 1 21 9.414V19a2 2 0 0 1-2 2H8m-4-4 4 4m0-4v4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900">Bukti Faktur</h2>
                        <p class="text-xs text-slate-500">Kelola foto faktur pembelian untuk dokumentasi.</p>
                    </div>
                </div>
            </div>

            <div class="p-5 sm:p-6 relative z-10">
                <label for="foto_faktur"
                       class="group flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300/80 bg-white/40 px-6 py-8 text-center transition hover:border-blue-400 hover:bg-white/60">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 group-hover:text-blue-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 16V4m0 0L8 8m4-4 4 4M5 20h14"/>
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

        {{-- Actions --}}
        <div class="sticky bottom-4 z-10 rounded-2xl border border-white/60 bg-white/80 p-4 shadow-lg backdrop-blur-xl">
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="hidden text-xs text-slate-500 font-medium sm:block">
                    Pastikan data yang diperbarui sudah benar sebelum disimpan.
                </p>
                <div class="flex w-full gap-3 sm:w-auto">
                    <a href="{{ route('riwayat-order.index') }}"
                       class="flex-1 rounded-xl border border-slate-200/60 bg-white px-6 py-3 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:shadow-sm sm:flex-none">
                        Batal
                    </a>
                    <button type="submit"
                            class="flex-1 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-3 text-sm font-semibold text-white shadow-md shadow-blue-500/30 transition-all hover:shadow-lg hover:shadow-blue-500/40 hover:-translate-y-0.5 sm:flex-none">
                        Simpan Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
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
