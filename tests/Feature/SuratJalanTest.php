<?php

use App\Models\SuratJalan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

/**
 * Surat jalan: halaman daftar, form, dan halaman pantau manajer.
 * Yang diuji bukan cuma "status 200", tapi data benar-benar muncul dan
 * satu surat tampil sebagai satu kartu berisi seluruh barangnya.
 */
test('halaman surat jalan menampilkan barang yang tersimpan', function () {
    $tinter = User::factory()->create(['role' => 'tinter']);

    $item = SuratJalan::create([
        'tanggal' => '2026-10-01',
        'nomor_surat' => 'SJ/BP-WT/2026/10/001',
        'nomor_urut' => 1,
        'kode_barang' => 'MAT-001',
        'nama_barang' => 'Autobase Clear Coat 2:1 High Gloss',
        'kemasan_barang' => 'Kaleng (1 Liter)',
        'jumlah' => 20,
        'asal_penyimpanan' => 'Mix Room A-01',
    ]);

    actingAs($tinter)
        ->get(route('surat-jalan.index'))
        ->assertOk()
        ->assertSee('SJ/BP-WT/2026/10/001')
        ->assertSee('Autobase Clear Coat 2:1 High Gloss')
        ->assertSee('Kaleng (1 Liter)')
        ->assertSee('Mix Room A-01')
        // Satu surat = kartu yang bisa dibuka-tutup
        ->assertSee('Daftar Surat &amp; Barang', escape: false)
        ->assertSee('<details', escape: false)
        ->assertSee('Buka rincian barang (1)', escape: false)
        // Barang bisa diubah / dihapus dari dalam kartu
        ->assertSee(route('surat-jalan.edit', $item), escape: false)
        ->assertSee(route('surat-jalan.destroy', $item), escape: false);
});

test('banyak barang dengan nomor surat sama tampil sebagai satu surat', function () {
    $tinter = User::factory()->create(['role' => 'tinter']);

    foreach ([
        ['MAT-001', 'Autobase Clear Coat', 'Kaleng (1 Liter)', 20],
        ['MAT-014', 'Autocryl Plus Hardener', 'Kaleng (0,5 Liter)', 12],
        ['CON-102', 'Thinner Slow Solvent', 'Jeriken (5 Liter)', 6],
    ] as $i => [$kode, $nama, $kemasan, $jumlah]) {
        SuratJalan::create([
            'tanggal' => '2026-10-01',
            'nomor_surat' => 'SJ/BP-WT/2026/10/001',
            'nomor_urut' => $i + 1,
            'kode_barang' => $kode,
            'nama_barang' => $nama,
            'kemasan_barang' => $kemasan,
            'jumlah' => $jumlah,
        ]);
    }

    $halaman = actingAs($tinter)->get(route('surat-jalan.index'))->assertOk();

    // Nomor surat muncul sekali (dikelompokkan), bukan tiga kartu terpisah.
    expect(preg_match_all('/SJ\/BP-WT\/2026\/10\/001/', $halaman->getContent()))->toBe(1);
    $halaman->assertSee('3 barang');
    $halaman->assertSee('Buka rincian barang (3)', escape: false);

    // Semua barang di dalam surat ikut tampil setelah dibuka.
    $halaman->assertSee('Autobase Clear Coat');
    $halaman->assertSee('Autocryl Plus Hardener');
    $halaman->assertSee('Thinner Slow Solvent');
});

test('tinter bisa menyimpan surat jalan dan nomor urut diisi otomatis', function () {
    $tinter = User::factory()->create(['role' => 'tinter']);

    $payload = [
        'cabang_area' => 'Wira Toyota Banjarmasin',
        'tanggal' => '2026-10-01',
        'nomor_surat' => 'SJ/TEST/001',
        'nama_barang' => 'Cat Dasar',
        'jumlah' => 5,
    ];

    actingAs($tinter)->post(route('surat-jalan.store'), $payload)
        ->assertRedirect(route('surat-jalan.index'));

    // Barang kedua tanpa nomor urut harus dapat urutan 2.
    actingAs($tinter)->post(route('surat-jalan.store'), array_merge($payload, ['nama_barang' => 'Cat Akhir']))
        ->assertRedirect(route('surat-jalan.index'));

    $urutan = SuratJalan::where('nomor_surat', 'SJ/TEST/001')->orderBy('nomor_urut')->pluck('nomor_urut')->all();

    expect($urutan)->toBe([1, 2]);
});

test('form tambah dan edit surat jalan bisa dibuka', function () {
    $tinter = User::factory()->create(['role' => 'tinter']);

    $item = SuratJalan::create([
        'tanggal' => '2026-10-01',
        'nomor_surat' => 'SJ/EDIT/001',
        'nama_barang' => 'Primer',
        'jumlah' => 2,
    ]);

    actingAs($tinter)->get(route('surat-jalan.create'))->assertOk()->assertSee('Tambah Surat Jalan');
    actingAs($tinter)->get(route('surat-jalan.edit', $item))->assertOk()->assertSee('Edit Surat Jalan');
});

test('manajer bisa memantau surat jalan tanpa aksi ubah data', function () {
    $manajer = User::factory()->create(['role' => 'manajer']);

    $item = SuratJalan::create([
        'tanggal' => '2026-10-01',
        'nomor_surat' => 'SJ/PANTAU/001',
        'nama_barang' => 'Clear Coat',
        'jumlah' => 10,
    ]);

    actingAs($manajer)
        ->get(route('manajer.surat-jalan'))
        ->assertOk()
        ->assertSee('Surat Jalan (Pantau)')
        ->assertSee('Mode Pantau')
        ->assertSee('SJ/PANTAU/001')
        ->assertSee('Clear Coat')
        // Mode pantau: tidak ada tautan ubah data.
        ->assertDontSee(route('surat-jalan.edit', $item), escape: false)
        ->assertDontSee(route('surat-jalan.destroy', $item), escape: false);
});

test('tamu tidak bisa membuka surat jalan', function () {
    $this->get(route('surat-jalan.index'))->assertRedirect(route('login'));
});
