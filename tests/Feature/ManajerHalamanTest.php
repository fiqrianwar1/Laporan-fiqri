<?php

use App\Models\LaporanOplosan;
use App\Models\RiwayatOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

/**
 * Halaman khusus manajer: hanya role manajer yang boleh masuk,
 * dan isinya harus benar-benar data pengawasan (bukan formulir isian).
 */
beforeEach(function () {
    RiwayatOrder::create([
        'tanggal' => '2026-09-01',
        'no_bukti_faktur' => 'W.PJ.260901010',
        'kode_barang' => '4900',
        'nama_barang' => 'PERMANENT DOYBLE TAPE 24MM X 4,5 M',
        'qty' => 4,
        'satuan' => 'ROL',
        'harga_satuan' => 105000,
        'diskon_persen' => 5,
    ]);

    LaporanOplosan::create([
        'tanggal' => '2026-09-01',
        'no_plat' => 'DA 1234 XY',
        'kode_warna_unit' => 'Super White 040',
        'no_bukti_nota' => 'INV/2026/001',
        'rincian_bahan' => 'Thinner, Cat Dasar',
        'qty_cc' => 500,
        'harga_nota' => 250000,
    ]);
});

test('manajer bisa membuka dashboard pengawasan', function () {
    $manajer = User::factory()->create(['role' => 'manajer']);

    actingAs($manajer)
        ->get(route('manajer.dashboard'))
        ->assertOk()
        // Judul & penanda mode pengawasan tampil
        ->assertSee('Dashboard Manajer')
        ->assertSee('Mode Pengawasan')
        // Kolom khas pengawasan
        ->assertSee('Perlu Diperiksa')
        ->assertSee('Biaya per CC')
        ->assertSee('Unit Pemakai Terbanyak')
        ->assertSee('Penyedot Anggaran')
        ->assertSee('Aktivitas Terbaru')
        // Data asli ikut terhitung
        ->assertSee('DA 1234 XY');
});

test('manajer bisa membuka halaman pantau riwayat order', function () {
    $manajer = User::factory()->create(['role' => 'manajer']);

    actingAs($manajer)
        ->get(route('manajer.riwayat-order'))
        ->assertOk()
        ->assertSee('Riwayat Order (Pantau)')
        ->assertSee('Mode Pantau')
        ->assertSee('PERMANENT DOYBLE TAPE 24MM X 4,5 M')
        ->assertSee('Rp 399.000')
        ->assertSee('Total Potongan Diskon')
        ->assertSee('5 Pembelian Terbesar')
        // Ditampilkan sebagai nota yang bisa dibuka-tutup
        ->assertSee('Daftar Nota &amp; Item', escape: false)
        ->assertSee('<details', escape: false)
        // Mode pantau: tidak ada tombol ubah data di dalam nota
        ->assertDontSee('>Hapus<', escape: false);
});

test('manajer bisa membuka halaman pantau laporan oplosan', function () {
    $manajer = User::factory()->create(['role' => 'manajer']);

    actingAs($manajer)
        ->get(route('manajer.laporan-oplosan'))
        ->assertOk()
        ->assertSee('Laporan Oplosan (Pantau)')
        ->assertSee('Mode Pantau')
        ->assertSee('DA 1234 XY')
        ->assertSee('Super White 040')
        ->assertSee('Total Qty (CC/LTR)')
        ->assertSee('Daftar Nota &amp; Item', escape: false);
});

test('halaman pantau manajer tidak menyediakan aksi ubah data', function () {
    $manajer = User::factory()->create(['role' => 'manajer']);

    $laporan = LaporanOplosan::first();
    $order = RiwayatOrder::first();

    // Tidak ada tautan edit/hapus sama sekali di halaman pantau.
    actingAs($manajer)
        ->get(route('manajer.laporan-oplosan'))
        ->assertOk()
        ->assertDontSee(route('laporan-oplosan.edit', $laporan), escape: false)
        ->assertDontSee(route('laporan-oplosan.destroy', $laporan), escape: false);

    actingAs($manajer)
        ->get(route('manajer.riwayat-order'))
        ->assertOk()
        ->assertDontSee(route('riwayat-order.edit', $order), escape: false)
        ->assertDontSee(route('riwayat-order.destroy', $order), escape: false);
});

test('tinter tidak boleh membuka halaman manajer', function () {
    $tinter = User::factory()->create(['role' => 'tinter']);

    actingAs($tinter)->get(route('manajer.dashboard'))->assertForbidden();
    actingAs($tinter)->get(route('manajer.riwayat-order'))->assertForbidden();
    actingAs($tinter)->get(route('manajer.laporan-oplosan'))->assertForbidden();
});

test('tamu diarahkan ke login sebelum masuk halaman manajer', function () {
    $this->get(route('manajer.dashboard'))->assertRedirect(route('login'));
});

test('login manajer diarahkan ke dashboard pengawasan', function () {
    $manajer = User::factory()->create([
        'role' => 'manajer',
        'password' => 'password',
    ]);

    $this->post(route('login'), [
        'email' => $manajer->email,
        'password' => 'password',
    ])->assertRedirect(route('manajer.dashboard'));
});
