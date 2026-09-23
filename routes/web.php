<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanOplosanController;
use App\Http\Controllers\ManajerController;
use App\Http\Controllers\RiwayatOrderController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ===== Auth =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Route export PDF (harus di atas resource biar nggak ketiban route lain)
// Halaman preview: dokumen tampil dulu di browser, baru bisa print/simpan.
Route::get('/laporan-oplosan/preview/pdf', [LaporanOplosanController::class, 'previewPdf'])
    ->name('laporan-oplosan.preview');

Route::get('/riwayat-order/preview/pdf', [RiwayatOrderController::class, 'previewPdf'])
    ->name('riwayat-order.preview');

// Stream PDF mentah (dipakai sebagai src iframe di halaman preview).
Route::get('/laporan-oplosan/preview/pdf/raw', [LaporanOplosanController::class, 'streamPdf'])
    ->name('laporan-oplosan.pdf.stream');

Route::get('/riwayat-order/preview/pdf/raw', [RiwayatOrderController::class, 'streamPdf'])
    ->name('riwayat-order.pdf.stream');

// Download langsung (tanpa preview).
Route::get('/laporan-oplosan/export/pdf', [LaporanOplosanController::class, 'exportPdf'])
    ->name('laporan-oplosan.pdf');

Route::get('/riwayat-order/export/pdf', [RiwayatOrderController::class, 'exportPdf'])
    ->name('riwayat-order.pdf');

// ===== Halaman yang butuh login =====
// Semua user yang sudah login boleh melihat (index) & export.
// Tapi hanya 'tinter' yang boleh menambah/mengubah/menghapus laporan.
Route::middleware('auth')->group(function () {

    // Dashboard: ringkasan statistik oplosan & riwayat order.
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ===== Khusus MANAJER =====
    // Tampilan pengawasan: beda fokus dengan halaman tinter. Yang ditekankan
    // di sini adalah pemantauan biaya, penyimpangan, dan peringkat unit -
    // bukan formulir pengisian (manajer memang tidak mengisi data).
    Route::middleware('role:manajer')->prefix('manajer')->name('manajer.')->group(function () {
        Route::get('/', [ManajerController::class, 'dashboard'])->name('dashboard');
        Route::get('/riwayat-order', [ManajerController::class, 'riwayatOrder'])->name('riwayat-order');
        Route::get('/laporan-oplosan', [ManajerController::class, 'laporanOplosan'])->name('laporan-oplosan');
    });

    // Route export PDF (harus di atas resource biar nggak ketiban route lain)
    Route::get('/laporan-oplosan/preview/pdf', [LaporanOplosanController::class, 'previewPdf'])
        ->name('laporan-oplosan.preview');

    Route::get('/riwayat-order/preview/pdf', [RiwayatOrderController::class, 'previewPdf'])
        ->name('riwayat-order.preview');

    Route::get('/laporan-oplosan/preview/pdf/raw', [LaporanOplosanController::class, 'streamPdf'])
        ->name('laporan-oplosan.pdf.stream');

    Route::get('/riwayat-order/preview/pdf/raw', [RiwayatOrderController::class, 'streamPdf'])
        ->name('riwayat-order.pdf.stream');

    Route::get('/laporan-oplosan/export/pdf', [LaporanOplosanController::class, 'exportPdf'])
        ->name('laporan-oplosan.pdf');

    Route::get('/riwayat-order/export/pdf', [RiwayatOrderController::class, 'exportPdf'])
        ->name('riwayat-order.pdf');

    // Lihat data: semua yang sudah login (tinter & manajer)
    Route::resource('laporan-oplosan', LaporanOplosanController::class)
        ->only(['index']);

    Route::resource('riwayat-order', RiwayatOrderController::class)
        ->only(['index']);

    // Isi/Ubah/Hapus data: HANYA tinter (pengisi laporan)
    Route::middleware('role:tinter')->group(function () {
        Route::resource('laporan-oplosan', LaporanOplosanController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy']);

        Route::resource('riwayat-order', RiwayatOrderController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy']);
    });
});
