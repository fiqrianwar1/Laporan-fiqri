@extends('layouts.app')
{{--
    Layout khusus MANAJER.

    Tugasnya cuma mengatur menu sidebar & bottom-nav supaya manajer
    melihat menu pengawasan (bukan menu pengisian). Semua isi kartu,
    filter, dan tabel tetap memakai komponen yang sama dari
    layouts.app, jadi perawatannya satu tempat saja.
--}}
@php
    $menuUtama = [
        [
            'label' => 'Dashboard',
            'desc'  => 'Ringkasan & pengawasan',
            'route' => 'manajer.dashboard',
            'aktif' => request()->routeIs('manajer.dashboard'),
            'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        ],
        [
            'label' => 'Laporan Oplosan',
            'desc'  => 'Pantau tinting per unit',
            'route' => 'manajer.laporan-oplosan',
            'aktif' => request()->routeIs('manajer.laporan-oplosan'),
            'icon'  => 'M9 3v2m6-2v2M4 8h16M6 6h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2z',
        ],
        [
            'label' => 'Riwayat Order',
            'desc'  => 'Pantau belanja bahan',
            'route' => 'manajer.riwayat-order',
            'aktif' => request()->routeIs('manajer.riwayat-order'),
            'icon'  => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        ],
        [
            'label' => 'Laporan Harian',
            'desc'  => 'Pantau oplosan harian',
            'route' => 'manajer.laporan-harian',
            'aktif' => request()->routeIs('manajer.laporan-harian'),
            'icon'  => 'M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        ],
    ];
@endphp
