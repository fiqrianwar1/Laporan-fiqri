@extends('layouts.app')

@section('title', 'Tambah Laporan Harian')

@section('content')
<div class="mx-auto max-w-4xl space-y-5">
    <div class="page-head">
        <div>
            <div class="pill pill-blue mb-2">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-600"></span>
                Tambah Data
            </div>
            <h1 class="page-title">Tambah Catatan Harian</h1>
            <p class="page-sub">Catat satu pekerjaan oplosan cat: unit, bahan, volume, jam kerja, dan hasilnya.</p>
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

    <form action="{{ route('laporan-harian.store') }}" method="POST" class="space-y-5">
        @csrf

        @include('laporan_harian._form', [
            'data' => [],
            'tombol' => 'Simpan Catatan',
        ])
    </form>
</div>
@endsection
