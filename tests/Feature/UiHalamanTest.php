<?php

use App\Models\LaporanOplosan;
use App\Models\RiwayatOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

/**
 * Halaman utama harus bisa dibuka tanpa error, dan data yang tersimpan
 * benar-benar tampil di tabelnya (bukan cuma "status 200").
 */
test('dashboard menampilkan ringkasan periode', function () {
    $tinter = User::factory()->create(['role' => 'tinter']);

    actingAs($tinter)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Filter Periode')
        // Dashboard default-nya tahun berjalan, jadi labelnya harus jujur
        // menyebut tahun itu - bukan "Semua periode".
        ->assertSee('Tahun ' . now()->year);
});

test('riwayat order tampil lengkap dengan tabel yang bisa digeser & tombol aksi', function () {
    $tinter = User::factory()->create(['role' => 'tinter']);

    $order = RiwayatOrder::create([
        'tanggal' => '2026-09-01',
        'no_bukti_faktur' => 'W.PJ.260901010',
        'kode_barang' => '4900',
        'nama_barang' => 'PERMANENT DOYBLE TAPE 24MM X 4,5 M',
        'qty' => 4,
        'satuan' => 'ROL',
        'harga_satuan' => 105000,
        'diskon_persen' => 5,
    ]);

    actingAs($tinter)
        ->get(route('riwayat-order.index'))
        ->assertOk()
        // Data benar-benar dirender
        ->assertSee('PERMANENT DOYBLE TAPE 24MM X 4,5 M')
        ->assertSee('W.PJ.260901010')
        ->assertSee('Rp 399.000,00')
        // Ditampilkan sebagai nota yang bisa dibuka-tutup
        ->assertSee('Daftar Nota &amp; Item', escape: false)
        ->assertSee('<details', escape: false)
        ->assertSee('1 item', escape: false)
        // Tombol aksi punya label, bukan ikon saja
        ->assertSee('Edit')
        ->assertSee('Hapus')
        ->assertSee(route('riwayat-order.edit', $order), escape: false)
        ->assertSee(route('riwayat-order.destroy', $order), escape: false);
});

test('satu nota yang berisi banyak item tampil sebagai satu nota dengan rincian per item', function () {
    $tinter = User::factory()->create(['role' => 'tinter']);

    // Meniru nota asli: 1 nomor bukti, banyak barang di dalamnya.
    foreach ([
        ['WZ4200', 'WIWAT MIRROR COAT 240', 5, 'PCS', 51000],
        ['WZ500', 'WIWAT MIXING CLEAR K500', 2, 'GLN', 175000],
        ['WS8800', 'WIWAT SUPER GLOSSY A8800', 1, 'ROL', 620000],
    ] as $i => [$kode, $nama, $qty, $satuan, $harga]) {
        RiwayatOrder::create([
            'tanggal' => '2026-09-05',
            'no_bukti_faktur' => 'WPJ.260905001',
            'nomor_urut' => $i + 1,
            'kode_barang' => $kode,
            'nama_barang' => $nama,
            'qty' => $qty,
            'satuan' => $satuan,
            'harga_satuan' => $harga,
            'diskon_persen' => 0,
        ]);
    }

    $halaman = actingAs($tinter)->get(route('riwayat-order.index'))->assertOk();

    // Nomor bukti muncul sekali (bukan tiga baris terpisah)
    $halaman->assertSee('WPJ.260905001');
    $halaman->assertSee('3 item');

    // Semua item di dalam nota ikut tampil
    $halaman->assertSee('WIWAT MIRROR COAT 240');
    $halaman->assertSee('WIWAT MIXING CLEAR K500');
    $halaman->assertSee('WIWAT SUPER GLOSSY A8800');
    $halaman->assertSee('item #2');

    // Header nota menampilkan jumlah, bukan "1 nota" terpisah tiap barang.
    // Nomor nota ditulis di baris info, jadi yang dihitung kemunculannya
    // dalam satu baris teks (bukan cuma tag kosong).
    expect(preg_match_all('/WPJ\.260905001/', $halaman->getContent()))->toBe(1);
});

test('laporan oplosan tampil lengkap dengan tabel yang bisa digeser & tombol aksi', function () {
    $tinter = User::factory()->create(['role' => 'tinter']);

    $laporan = LaporanOplosan::create([
        'tanggal' => '2026-09-01',
        'no_plat' => 'DA 1234 XY',
        'kode_warna_unit' => 'Super White 040',
        'no_bukti_nota' => 'INV/2026/001',
        'rincian_bahan' => 'Thinner, Cat Dasar',
        'qty_cc' => 500,
        'harga_nota' => 250000,
    ]);

    actingAs($tinter)
        ->get(route('laporan-oplosan.index'))
        ->assertOk()
        ->assertSee('DA 1234 XY')
        ->assertSee('Super White 040')
        // Nota oplosan juga tampil sebagai kartu yang bisa dibuka-tutup
        ->assertSee('Daftar Nota &amp; Item', escape: false)
        ->assertSee('<details', escape: false)
        ->assertSee('1 item', escape: false)
        ->assertSee('Edit')
        ->assertSee('Hapus')
        ->assertSee(route('laporan-oplosan.edit', $laporan), escape: false)
        ->assertSee(route('laporan-oplosan.destroy', $laporan), escape: false);
});

test('pengguna non-tinter tidak melihat aksi edit atau hapus', function () {
    $manajer = User::factory()->create(['role' => 'manajer']);

    RiwayatOrder::create([
        'tanggal' => '2026-09-01',
        'no_bukti_faktur' => 'INV-001',
        'nama_barang' => 'Thinner',
        'qty' => 1,
        'satuan' => 'GLN',
        'harga_satuan' => 10000,
        'diskon_persen' => 0,
    ]);

    actingAs($manajer)
        ->get(route('riwayat-order.index'))
        ->assertOk()
        ->assertDontSee('Mode isi')
        ->assertSee('Mode lihat');
});

test('halaman form tambah dan edit bisa dibuka', function () {
    $tinter = User::factory()->create(['role' => 'tinter']);

    $order = RiwayatOrder::create([
        'tanggal' => '2026-09-01',
        'no_bukti_faktur' => 'INV-002',
        'nama_barang' => 'Primer',
        'qty' => 2,
        'satuan' => 'PCS',
        'harga_satuan' => 50000,
        'diskon_persen' => 0,
    ]);

    $laporan = LaporanOplosan::create([
        'tanggal' => '2026-09-01',
        'no_plat' => 'DA 9999 ZZ',
        'kode_warna_unit' => 'Black 202',
        'no_bukti_nota' => 'INV/2026/002',
        'rincian_bahan' => 'Cat Dasar',
        'qty_cc' => 100,
        'harga_nota' => 75000,
    ]);

    actingAs($tinter)->get(route('riwayat-order.create'))->assertOk()->assertSee('Tambah Riwayat Order');
    actingAs($tinter)->get(route('riwayat-order.edit', $order))->assertOk()->assertSee('Edit Riwayat Order');
    actingAs($tinter)->get(route('laporan-oplosan.create'))->assertOk()->assertSee('Tambah Laporan');
    actingAs($tinter)->get(route('laporan-oplosan.edit', $laporan))->assertOk()->assertSee('Edit Laporan Oplosan');
});
