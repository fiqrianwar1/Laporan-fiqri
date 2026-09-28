<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanOplosan extends Model
{
    use HasFactory;

    protected $fillable = [
        'cabang_area',
        'tanggal',
        'no_plat',
        'kode_warna_unit',
        'no_bukti_nota',
        'nomor_urut',
        'rincian_bahan',
        'qty_cc',
        'harga_nota',
        'foto_nota',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'harga_nota' => 'decimal:2',
        'qty_cc' => 'integer',
        'nomor_urut' => 'integer',
    ];

    /**
     * Laporan oplosan tidak memakai diskon, jadi potongannya selalu nol.
     * Accessor ini disediakan supaya tabel/PDF bisa memakai nama kolom yang
     * sama dengan riwayat order.
     */
    public function getNominalDiskonAttribute(): float
    {
        return 0.0;
    }

    /**
     * Nomor bukti nota tanpa spasi/tanda baca - dipakai sebagai kunci
     * pengelompokan item yang berasal dari satu nota yang sama.
     */
    public function getNomorNotaAttribute(): string
    {
        $nomor = preg_replace('/[^A-Za-z0-9]/', '', (string) $this->no_bukti_nota);

        return $nomor !== '' ? strtoupper($nomor) : 'TANPA-NOMOR';
    }
}
