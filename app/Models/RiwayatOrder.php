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
        'total_baris',
        'foto_faktur',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'harga_satuan' => 'decimal:2',
        'diskon_persen' => 'decimal:2',
        'total_baris' => 'decimal:2',
        'qty' => 'integer',
        'nomor_urut' => 'integer',
    ];

    /**
     * Total sebelum diskon.
     *
     * Kalau faktur sudah mencantumkan nilai total barisnya, nilai itulah yang
     * dipakai - sebab pada faktur Wira Toyota kolom qty berisi volume cat
     * (CC/LTR), bukan jumlah barang, jadi qty x harga satuan tidak berlaku.
     * Kalau kolom total_baris kosong (faktur lama), baru dihitung qty x harga.
     */
    public function getTotalKotorAttribute()
    {
        if ($this->total_baris !== null) {
            return (float) $this->total_baris;
        }

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
