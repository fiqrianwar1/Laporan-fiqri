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

    /**
     * Total sebelum diskon (qty x harga satuan). Dipakai untuk menampilkan
     * potongan dalam rupiah, bukan hanya persennya.
     */
    public function getTotalKotorAttribute()
    {
        return $this->qty * (float) $this->harga_satuan;
    }

    /**
     * Potongan diskon dalam rupiah: total kotor x diskon persen.
     * Dihitung dari angka mentah (bukan yang sudah dibulatkan) supaya
     * nominalnya pas dengan selisih total kotor - total item.
     */
    public function getNominalDiskonAttribute()
    {
        return $this->total_kotor * ((float) $this->diskon_persen / 100);
    }

    public function getTotalItemAttribute()
    {
        return $this->total_kotor - $this->nominal_diskon;
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
