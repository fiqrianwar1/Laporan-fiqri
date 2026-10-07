@extends('layouts.app')

@section('title', 'Tambah Surat Jalan')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    <div class="page-head">
        <div>
            <div class="pill pill-blue mb-2">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-600"></span>
                Tambah Data
            </div>
            <h1 class="page-title">Tambah Surat Jalan</h1>
            <p class="page-sub">Catat satu barang yang dikirim: nomor surat, tujuan, barang, kemasan, dan jumlahnya.</p>
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

    <form action="{{ route('surat-jalan.store') }}" method="POST" class="space-y-5">
        @csrf

        @include('surat_jalan._form', [
            'data' => [],
            'tombol' => 'Simpan Surat Jalan',
        ])
    </form>
</div>
@endsection
