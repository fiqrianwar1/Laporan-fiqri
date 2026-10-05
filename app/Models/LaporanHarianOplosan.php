<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Laporan harian oplosan cat (paint mixing / tinter).
 *
 * Satu baris = satu pekerjaan oplosan: warna apa yang dioplos untuk mobil
 * mana, berapa volumenya, dikerjakan jam berapa, dan hasil pencocokan
 * warnanya. Tidak ada konsep nota di sini - formnya memang rekap harian.
 */
class LaporanHarianOplosan extends Model
{
    use HasFactory;

    protected $fillable = [
        'cabang_area',
        'tanggal',
        'plat_nomor',
        'kode_warna',
        'tipe_mobil',
        'bahan_cat',
        'volume_cc',
        'jam_dibuat',
        'jam_selesai',
        'durasi_menit',
        'hasil_matching',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'volume_cc' => 'integer',
        'durasi_menit' => 'integer',
    ];

    /**
     * Hitung durasi (menit) dari jam dibuat & jam selesai.
     *
     * Nilainya dipakai hanya kalau form tidak mengirim durasi manual:
     * jam selesai yang lebih awal dari jam dibuat dianggap lewat tengah
     * malam (mis. mulai 23.50, selesai 00.20 = 30 menit).
     */
    public static function hitungDurasi(?string $jamDibuat, ?string $jamSelesai): ?int
    {
        if (blank($jamDibuat) || blank($jamSelesai)) {
            return null;
        }

        try {
            $mulai = Carbon::parse($jamDibuat);
            $selesai = Carbon::parse($jamSelesai);
        } catch (\Throwable $e) {
            return null;
        }

        if ($selesai->lessThan($mulai)) {
            $selesai->addDay();
        }

        return (int) $mulai->diffInMinutes($selesai);
    }
}
