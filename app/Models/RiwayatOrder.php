<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'cabang_area',
        'tanggal',
        'no_bukti_faktur',
        'nomor_urut',
        'kode_barang',
        'nama_barang',
        'qty',
        'satuan',
        'harga_satuan',
        'diskon_persen',
        'foto_faktur',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'harga_satuan' => 'decimal:2',
        'diskon_persen' => 'decimal:2',
        'qty' => 'integer',
        'nomor_urut' => 'integer',
    ];

    public function getTotalItemAttribute()
    {
        $totalKotor = $this->qty * $this->harga_satuan;
        $diskon = $totalKotor * ($this->diskon_persen / 100);

        return $totalKotor - $diskon;
    }

    /**
     * Nomor bukti faktur tanpa spasi/tanda baca - dipakai sebagai kunci
     * pengelompokan item yang berasal dari satu nota yang sama.
     */
    public function getNomorBuktiAttribute(): string
    {
        $nomor = preg_replace('/[^A-Za-z0-9]/', '', (string) $this->no_bukti_faktur);

        return $nomor !== '' ? strtoupper($nomor) : 'TANPA-NOMOR';
    }
}
