@extends('layouts.app')

@section('title', 'Edit Laporan Harian')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    <div class="page-head">
        <div>
            <div class="pill pill-amber mb-2">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"></span>
                Mode Edit
            </div>
            <h1 class="page-title">Edit Catatan Harian</h1>
            <p class="page-sub">Perbarui satu baris catatan oplosan yang sudah tersimpan.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('laporan-harian.index') }}" class="btn btn-outline btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('laporan-harian.update', $baris) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        @include('laporan_harian._form', [
            'data' => [
                'cabang_area' => $baris->cabang_area,
                'tanggal' => optional($baris->tanggal)->format('Y-m-d'),
                'plat_nomor' => $baris->plat_nomor,
                'kode_warna' => $baris->kode_warna,
                'tipe_mobil' => $baris->tipe_mobil,
                'bahan_cat' => $baris->bahan_cat,
                'volume_cc' => $baris->volume_cc,
                'jam_dibuat' => $baris->jam_dibuat,
                'jam_selesai' => $baris->jam_selesai,
                'durasi_menit' => $baris->durasi_menit,
                'hasil_matching' => $baris->hasil_matching,
                'keterangan' => $baris->keterangan,
            ],
            'tombol' => 'Simpan Perubahan',
        ])
    </form>
</div>
@endsection
