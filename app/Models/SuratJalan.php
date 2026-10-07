<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Surat jalan / barang keluar gudang.
 *
 * Satu baris = satu barang dalam satu surat. Barang-barang yang nomor
 * suratnya sama (dan tanggalnya sama) dikelompokkan jadi satu surat oleh
 * NotaGrouper, jadi bentuknya sama dengan nota oplosan & riwayat order.
 */
class SuratJalan extends Model
{
    use HasFactory;

    protected $fillable = [
        'cabang_area',
        'tanggal',
        'nomor_surat',
        'nomor_urut',
        'kode_barang',
        'nama_barang',
        'kemasan_barang',
        'jumlah',
        'asal_penyimpanan',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nomor_urut' => 'integer',
        'jumlah' => 'integer',
    ];

    /**
     * Kunci pengelompokan barang satu surat: nomor surat tanpa spasi/tanda
     * baca. Dipakai NotaGrouper supaya "SJ/BT/1" dan "SJBT1" tetap dihitung
     * surat yang sama.
     */
    public function getNomorSuratBersihAttribute(): string
    {
        $nomor = preg_replace('/[^A-Za-z0-9]/', '', (string) $this->nomor_surat);

        return $nomor !== '' ? strtoupper($nomor) : 'TANPA-NOMOR';
    }
}
