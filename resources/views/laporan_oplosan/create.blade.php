@extends('layouts.app')

@section('title', 'Tambah Laporan Oplosan')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-blue-500/10 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-500/20 backdrop-blur-md">
                <span class="h-1.5 w-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                Manajemen Oplosan
            </div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Tambah Laporan Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Lengkapi form di bawah ini untuk mencatat data oplosan baru.</p>
        </div>
        <a href="{{ route('laporan-oplosan.index') }}"
           class="inline-flex items-center gap-2 bg-white/70 backdrop-blur-md border border-slate-200/60 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-white hover:shadow-md transition-all duration-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Form --}}
    <form action="{{ route('laporan-oplosan.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-sm border border-white/50 p-6 sm:p-8 space-y-6 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-500/5 rounded-full blur-3xl"></div>
        <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl"></div>
        
        @csrf

        <div class="grid gap-6 sm:grid-cols-2 relative z-10">
            <div class="sm:col-span-2">
                <label class="label">Cabang / Area</label>
                <select name="cabang_area" class="input appearance-none py-3">
                    @foreach (\App\Support\Cabang::daftar() as $cabang)
                        <option value="{{ $cabang }}" {{ old('cabang_area', \App\Support\Cabang::default()) === $cabang ? 'selected' : '' }}>
                            {{ $cabang }}
                        </option>
                    @endforeach
                </select>
                @error('cabang_area') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal') }}"
                       class="input py-3">
                @error('tanggal') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">No. Plat</label>
                <input type="text" name="no_plat" value="{{ old('no_plat') }}" placeholder="Contoh: DA 1234 XY"
                       class="input py-3">
                @error('no_plat') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Kode Warna / Unit</label>
                <input type="text" name="kode_warna_unit" value="{{ old('kode_warna_unit') }}" placeholder="Contoh: Super White 040"
                       class="input py-3">
                @error('kode_warna_unit') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">No. Bukti Nota</label>
                <input type="text" name="no_bukti_nota" value="{{ old('no_bukti_nota') }}" placeholder="Contoh: WPJ.260905001"
                       class="input py-3">
                <p class="mt-1.5 text-xs text-slate-400">Pakai nomor yang sama untuk semua item dalam satu nota.</p>
                @error('no_bukti_nota') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Nomor Urut Item <span class="font-normal text-slate-400">(opsional)</span></label>
                <input type="number" min="1" step="1" name="nomor_urut" value="{{ old('nomor_urut') }}" placeholder="1"
                       class="input py-3">
                <p class="mt-1.5 text-xs text-slate-400">Item ke-berapa di nota ini. Kosongkan agar otomatis di urutan terakhir.</p>
                @error('nomor_urut') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="label">Rincian Bahan / Oplosan</label>
                <input type="text" name="rincian_bahan" value="{{ old('rincian_bahan') }}" placeholder="Contoh: Thinner, Cat Dasar, Clear"
                       class="input py-3">
                @error('rincian_bahan') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Qty (CC/LTR)</label>
                <input type="number" name="qty_cc" value="{{ old('qty_cc') }}" placeholder="0"
                       class="input py-3">
                @error('qty_cc') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Harga Nota (Rp)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-sm font-medium text-slate-400">Rp</span>
                    <input type="number" step="0.01" name="harga_nota" value="{{ old('harga_nota') }}" placeholder="0"
                           class="input py-3 pl-10">
                </div>
                @error('harga_nota') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="label">Foto Nota</label>
                <div class="border-2 border-dashed border-slate-300/80 bg-white/40 rounded-xl p-6 text-center hover:bg-white/60 hover:border-blue-400 transition-all group">
                    <input type="file" name="foto_nota" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                </div>
                @error('foto_nota') <p class="form-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-6 mt-6 border-t border-slate-200/60 flex items-center justify-end gap-3 relative z-10">
            <a href="{{ route('laporan-oplosan.index') }}" class="btn btn-ghost w-full sm:w-auto">
                Batal
            </a>
            <button type="submit" class="btn btn-primary w-full sm:w-auto">
                Simpan Laporan
            </button>
        </div>
    </form>
</div>
@endsection
