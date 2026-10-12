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

{{-- Bukti foto --}}
<div class="form-card">
    <div class="form-card-head">
        <div class="stat-icon bg-gradient-to-br from-violet-500 to-fuchsia-500">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16.5V5a2 2 0 0 1 2-2h8.586A2 2 0 0 1 16 3.586L20.414 8A2 2 0 0 1 21 9.414V19a2 2 0 0 1-2 2H8m-4-4 4 4m0-4v4"/>
            </svg>
        </div>
        <div class="min-w-0">
            <h2 class="card-title">Foto Print</h2>
            <p class="card-sub">Kelola foto bukti surat jalan untuk dokumentasi.</p>
        </div>
    </div>

    <div class="p-4 sm:p-5">
        @isset($data['foto_print'])
            @if ($data['foto_print'])
                <div class="mb-4 flex flex-col gap-3 rounded-xl border border-blue-100 bg-blue-50/50 p-3.5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-blue-100 bg-white text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2 1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Foto print saat ini</p>
                            <p class="text-xs text-slate-500">Upload file baru untuk menggantinya.</p>
                        </div>
                    </div>
                    <a href="{{ Storage::url($data['foto_print']) }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-3.5 py-2 text-xs font-semibold text-blue-600 shadow-sm ring-1 ring-blue-100 transition hover:bg-blue-50">
                        Lihat Foto
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4m-3-7 5 5m0 0V4m0 5h-5"/>
                        </svg>
                    </a>
                </div>
            @endif
        @endisset

        <label for="foto_print"
               class="group flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300/80 bg-white/50 px-6 py-8 text-center transition hover:border-blue-400 hover:bg-white">
            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 transition-colors group-hover:text-blue-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0L8 8m4-4 4 4M5 20h14"/>
                </svg>
            </div>
            <p class="text-sm font-bold text-slate-700">Pilih foto print</p>
            <p class="mt-1 text-xs text-slate-400">JPG, JPEG, PNG atau format gambar lainnya</p>
            <input id="foto_print" type="file" name="foto_print" accept="image/*" class="hidden">
        </label>
        <p id="file-name" class="mt-2 hidden text-xs font-bold text-blue-600"></p>
        <p class="mt-2 text-xs text-slate-400">Foto print bersifat opsional dan dapat membantu dokumentasi pengiriman.</p>
        @error('foto_print') <p class="form-error">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Tombol aksi --}}
<div class="form-actions">
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('surat-jalan.index') }}" class="btn btn-outline flex-1 sm:flex-none">Batal</a>
        <button type="submit" class="btn btn-primary flex-1 sm:flex-none">{{ $tombol }}</button>
    </div>
</div>

<script>
// Tampilkan nama file yang dipilih supaya pengguna yakin fotonya masuk.
document.getElementById('foto_print')?.addEventListener('change', function () {
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
