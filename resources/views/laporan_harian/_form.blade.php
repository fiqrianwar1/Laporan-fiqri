{{--
    Isi form laporan harian oplosan.
    Dipakai halaman Tambah & Edit sekaligus supaya field-nya tidak pernah
    beda antara dua halaman itu.

    Variabel yang diharapkan: $pilihanMatching, $pilihanBahan, dan
    $data (nilai lama / nilai baris yang sedang diedit).
--}}
@php
    $nilai = fn ($field, $default = '') => old($field, $data[$field] ?? $default);
@endphp

{{-- Identitas pekerjaan --}}
<div class="form-card">
    <div class="form-card-head">
        <div class="stat-icon bg-gradient-to-br from-blue-500 to-blue-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 3v2m6-2v2M4 8h16M6 6h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <h2 class="card-title">Identitas Unit</h2>
            <p class="card-sub">Cabang, tanggal, nomor plat, kode warna, dan tipe mobilnya.</p>
        </div>
    </div>

    <div class="form-card-body">
        <div class="sm:col-span-2">
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

        <div>
            <label for="plat_nomor" class="label">Plat Nomor</label>
            <input id="plat_nomor" type="text" name="plat_nomor" value="{{ $nilai('plat_nomor') }}" placeholder="Contoh: DA 1234 XY" class="input">
            @error('plat_nomor') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="kode_warna" class="label">Kode Warna</label>
            <input id="kode_warna" type="text" name="kode_warna" value="{{ $nilai('kode_warna') }}" placeholder="Contoh: 040 Super White" class="input">
            @error('kode_warna') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="tipe_mobil" class="label">Tipe Mobil</label>
            <input id="tipe_mobil" type="text" name="tipe_mobil" value="{{ $nilai('tipe_mobil') }}" placeholder="Contoh: Innova Zenix" class="input">
            @error('tipe_mobil') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- Detail bahan & pekerjaan --}}
<div class="form-card">
    <div class="form-card-head">
        <div class="stat-icon bg-gradient-to-br from-emerald-400 to-emerald-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
            </svg>
        </div>
        <div class="min-w-0">
            <h2 class="card-title">Detail Oplosan</h2>
            <p class="card-sub">Bahan cat, volume, jam pengerjaan, dan hasil pencocokan warna.</p>
        </div>
    </div>

    <div class="form-card-body">
        <div>
            <label for="bahan_cat" class="label">Bahan Cat</label>
            <input id="bahan_cat" type="text" name="bahan_cat" value="{{ $nilai('bahan_cat') }}" list="daftar-bahan"
                   placeholder="Contoh: Wanda SB" class="input">
            <p class="mt-1.5 text-xs text-slate-400">Pilihan cepat: Autobase, Autocryl, Wanda SB, Wanda 2K.</p>
            <datalist id="daftar-bahan">
                @foreach ($pilihanBahan as $bahan)
                    <option value="{{ $bahan }}"></option>
                @endforeach
            </datalist>
            @error('bahan_cat') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="volume_cc" class="label">Volume (CC)</label>
            <input id="volume_cc" type="number" min="0" step="1" name="volume_cc" value="{{ $nilai('volume_cc') }}" placeholder="0" class="input">
            <p class="mt-1.5 text-xs text-slate-400">Boleh dikosongkan / 0 untuk pemakaian cat sisa.</p>
            @error('volume_cc') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="jam_dibuat" class="label">Jam Dibuat</label>
            <input id="jam_dibuat" type="time" name="jam_dibuat"
                   value="{{ $nilai('jam_dibuat') ? \Illuminate\Support\Str::of($nilai('jam_dibuat'))->substr(0, 5) : '' }}" class="input">
            @error('jam_dibuat') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="jam_selesai" class="label">Jam Selesai</label>
            <input id="jam_selesai" type="time" name="jam_selesai"
                   value="{{ $nilai('jam_selesai') ? \Illuminate\Support\Str::of($nilai('jam_selesai'))->substr(0, 5) : '' }}" class="input">
            @error('jam_selesai') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="durasi_menit" class="label">Durasi (menit) <span class="font-normal text-slate-400">(opsional)</span></label>
            <input id="durasi_menit" type="number" min="0" max="1440" step="1" name="durasi_menit"
                   value="{{ $nilai('durasi_menit') }}" placeholder="Otomatis dari jam" class="input">
            <p class="mt-1.5 text-xs text-slate-400">Kosongkan agar dihitung otomatis dari jam dibuat &amp; jam selesai.</p>
            @error('durasi_menit') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="hasil_matching" class="label">Hasil Matching</label>
            <div class="relative">
                <select id="hasil_matching" name="hasil_matching" class="input appearance-none pr-10">
                    @foreach ($pilihanMatching as $pilihan)
                        <option value="{{ $pilihan }}" {{ $nilai('hasil_matching', 'Sama') === $pilihan ? 'selected' : '' }}>
                            {{ $pilihan }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            @error('hasil_matching') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="keterangan" class="label">Keterangan <span class="font-normal text-slate-400">(opsional)</span></label>
            <input id="keterangan" type="text" name="keterangan" value="{{ $nilai('keterangan') }}" placeholder="Catatan tambahan bila perlu" class="input">
            @error('keterangan') <p class="form-error">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- Tombol aksi --}}
<div class="form-actions">
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('laporan-harian.index') }}" class="btn btn-outline flex-1 sm:flex-none">Batal</a>
        <button type="submit" class="btn btn-primary flex-1 sm:flex-none">{{ $tombol }}</button>
    </div>
</div>
