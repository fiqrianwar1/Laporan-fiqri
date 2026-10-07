@extends('layouts.app')

@section('title', 'Edit Surat Jalan')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    <div class="page-head">
        <div>
            <div class="pill pill-amber mb-2">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-500"></span>
                Mode Edit
            </div>
            <h1 class="page-title">Edit Surat Jalan</h1>
            <p class="page-sub">Perbarui satu barang pada surat jalan yang sudah tersimpan.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('surat-jalan.index') }}" class="btn btn-outline btn-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('surat-jalan.update', $surat) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        @include('surat_jalan._form', [
            'data' => [
                'cabang_area' => $surat->cabang_area,
                'tanggal' => optional($surat->tanggal)->format('Y-m-d'),
                'nomor_surat' => $surat->nomor_surat,
                'nomor_urut' => $surat->nomor_urut,
                'kirim_kepada' => $surat->kirim_kepada,
                'alamat_penerima' => $surat->alamat_penerima,
                'kode_barang' => $surat->kode_barang,
                'nama_barang' => $surat->nama_barang,
                'kemasan_barang' => $surat->kemasan_barang,
                'jumlah' => $surat->jumlah,
                'asal_penyimpanan' => $surat->asal_penyimpanan,
                'keterangan' => $surat->keterangan,
            ],
            'tombol' => 'Simpan Perubahan',
        ])
    </form>
</div>
@endsection
