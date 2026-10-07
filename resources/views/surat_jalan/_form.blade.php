{{--
    Isi form surat jalan.
    Dipakai halaman Tambah & Edit sekaligus supaya field-nya tidak pernah
    beda antara dua halaman itu.

    Variabel yang diharapkan: $data (nilai lama / nilai baris yang sedang
    diedit) dan opsional $saudaraSurat (barang lain dalam surat yang sama).
--}}
@php
    $nilai = fn ($field, $default = '') => old($field, $data[$field] ?? $default);
@endphp

{{-- Kepala surat --}}
<div class="form-card">
    <div class="form-card-head">
        <div class="stat-icon bg-gradient-to-br from-blue-500 to-blue-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <h2 class="card-title">Kepala Surat</h2>
            <p class="card-sub">Nomor surat, tanggal, dan cabang pengiriman.</p>
        </div>
    </div>

    <div class="form-card-body">
        <div>
            <label for="cabang_area" class="label">Cabang / Area</label>
            <select id="cabang_area" name="cabang_area" class="input">
                @foreach (\App\Support\Cabang::daftar() as $c)
                    <option value="{{ $c }}" {{ $nilai('cabang_area', \App\Support\Cabang::default()) === $c ? 'selected' : '' }}>
                        {{ $c }}
                    </option>
                @endforeach
            </select>
            @error('cabang_area') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="tanggal" class="label">Tanggal</label>
            <input id="tanggal" type="date" name="tanggal" value="{{ $nilai('tanggal') }}" class="input">
            @error('tanggal') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="nomor_surat" class="label">No. Surat</label>
            <input id="nomor_surat" type="text" name="nomor_surat" value="{{ $nilai('nomor_surat') }}"
                   placeholder="Contoh: SJ/BP-WT/2026/10/001" class="input">
            <p class="mt-1.5 text-xs text-slate-400">Isi nomor yang sama untuk menambah barang lain ke surat yang sama.</p>
            @error('nomor_surat') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- Barang yang dikirim --}}
<div class="form-card">
    <div class="form-card-head">
        <div class="stat-icon bg-gradient-to-br from-emerald-400 to-emerald-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div class="min-w-0">
            <h2 class="card-title">Barang Dikirim</h2>
            <p class="card-sub">Kode barang, nama, kemasan, jumlah, dan asal penyimpanan.</p>
        </div>
    </div>

    <div class="form-card-body">
        <div>
            <label for="kode_barang" class="label">Kode Barang</label>
            <input id="kode_barang" type="text" name="kode_barang" value="{{ $nilai('kode_barang') }}"
                   placeholder="Contoh: MAT-001" class="input">
            @error('kode_barang') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="nama_barang" class="label">Nama Barang / Cat</label>
            <input id="nama_barang" type="text" name="nama_barang" value="{{ $nilai('nama_barang') }}"
                   placeholder="Contoh: Autobase Clear Coat 2:1 High Gloss" class="input">
            @error('nama_barang') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="kemasan_barang" class="label">Kemasan Barang</label>
            <input id="kemasan_barang" type="text" name="kemasan_barang" value="{{ $nilai('kemasan_barang') }}"
                   placeholder="Contoh: Kaleng (1 Liter)" class="input">
            @error('kemasan_barang') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="jumlah" class="label">Jumlah</label>
            <input id="jumlah" type="number" min="1" step="1" name="jumlah" value="{{ $nilai('jumlah') }}" placeholder="0" class="input">
            @error('jumlah') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="asal_penyimpanan" class="label">Asal Penyimpanan</label>
            <input id="asal_penyimpanan" type="text" name="asal_penyimpanan" value="{{ $nilai('asal_penyimpanan') }}"
                   placeholder="Contoh: Gudang Utama A-01" class="input">
            @error('asal_penyimpanan') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="nomor_urut" class="label">No. Urut <span class="font-normal text-slate-400">(opsional)</span></label>
            <input id="nomor_urut" type="number" min="1" max="9999" step="1" name="nomor_urut"
                   value="{{ $nilai('nomor_urut') }}" placeholder="Otomatis" class="input">
            <p class="mt-1.5 text-xs text-slate-400">Kosongkan agar barang ditaruh di urutan terakhir surat ini.</p>
            @error('nomor_urut') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="keterangan" class="label">Keterangan <span class="font-normal text-slate-400">(opsional)</span></label>
            <input id="keterangan" type="text" name="keterangan" value="{{ $nilai('keterangan') }}"
                   placeholder="Contoh: Untuk stok ruang mixing" class="input">
            @error('keterangan') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

@isset($saudaraSurat)
    @if ($saudaraSurat->isNotEmpty())
        {{-- Info barang lain di surat yang sama, supaya pengguna tahu surat ini
             bukan surat terpisah - hanya salah satu barisnya yang diedit. --}}
        <div class="form-card">
            <div class="form-card-head">
                <div class="stat-icon bg-gradient-to-br from-slate-700 to-slate-900 text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="card-title">Barang Lain di Surat Ini</h2>
                    <p class="card-sub">Barang-barang berikut ikut tercatat pada nomor surat {{ $surat->nomor_surat }}.</p>
                </div>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($saudaraSurat as $lain)
                    <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-5">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $lain->nama_barang }}</p>
                            <p class="text-[11px] text-slate-500">
                                {{ $lain->kode_barang ?: '—' }} &middot; {{ $lain->kemasan_barang ?: '—' }}
                                &middot; item #{{ $lain->nomor_urut ?? '-' }}
                            </p>
                        </div>
                        <span class="angka shrink-0 text-sm font-extrabold text-slate-900">{{ number_format($lain->jumlah) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endisset

{{-- Tombol aksi --}}
<div class="form-actions">
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('surat-jalan.index') }}" class="btn btn-outline flex-1 sm:flex-none">Batal</a>
        <button type="submit" class="btn btn-primary flex-1 sm:flex-none">{{ $tombol }}</button>
    </div>
</div>
