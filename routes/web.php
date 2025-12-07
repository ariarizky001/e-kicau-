<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasLombaController;
use App\Http\Controllers\JuriController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\FormNominasiController;
use App\Http\Controllers\RekapLaporanController;
use App\Http\Controllers\RekapHasilController;
use App\Http\Controllers\LayarNominasiController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PengaturanEventController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard & Monitoring
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

    // Master Data
    Route::resource('kelas-lomba', KelasLombaController::class)->parameters([
        'kelas-lomba' => 'kelasLomba'
    ]);
    Route::post('/kelas-lomba/store-inline', [KelasLombaController::class, 'storeInline'])->name('kelas-lomba.store-inline');
    Route::get('/kelas-lomba/import', [KelasLombaController::class, 'showImport'])->name('kelas-lomba.import');
    Route::post('/kelas-lomba/import', [KelasLombaController::class, 'import'])->name('kelas-lomba.import.post');
    Route::get('/kelas-lomba/template/download', [KelasLombaController::class, 'downloadTemplate'])->name('kelas-lomba.download-template');
    Route::resource('juri', JuriController::class);
    Route::post('/juri/store-inline', [JuriController::class, 'storeInline'])->name('juri.store-inline');
    Route::resource('peserta', PesertaController::class);
    Route::get('/peserta/{kelasLomba}/grid', [PesertaController::class, 'showGrid'])->name('peserta.grid');
    Route::post('/peserta/{kelasLomba}/grid/store', [PesertaController::class, 'storeGrid'])->name('peserta.store-grid');
    Route::get('/peserta/{kelasLomba}/grid-settings', [PesertaController::class, 'gridSettings'])->name('peserta.grid-settings');
    Route::put('/peserta/{kelasLomba}/grid-config', [PesertaController::class, 'updateGridConfig'])->name('peserta.update-grid-config');
    Route::post('/peserta/{kelasLomba}/grid/copy', [PesertaController::class, 'copyFromKelas'])->name('peserta.copy-grid');
    Route::post('/peserta/{kelasLomba}/grid/reset', [PesertaController::class, 'resetGrid'])->name('peserta.reset-grid');

    // API Routes for AJAX
    Route::get('/api/peserta', [PesertaController::class, 'apiPeserta'])->name('api.peserta');
    Route::get('/api/grid-config/{kelasLomba}', [PesertaController::class, 'apiGridConfig'])->name('api.grid-config');
    Route::get('/api/grid-data/{kelasLomba}', [PesertaController::class, 'apiGridData'])->name('api.grid-data');
    Route::get('/api/slot-kosong', [PesertaController::class, 'getSlotKosong'])->name('api.slot-kosong');
    Route::post('/api/update-gantangan', [PesertaController::class, 'updateGantangan'])->name('api.update-gantangan');
    Route::post('/api/test-peserta', function () {
        return response()->json([
            'success' => true,
            'message' => 'Test endpoint is working',
            'timestamp' => now()
        ]);
    });

    // Operasional
    Route::resource('form-nominasi', FormNominasiController::class);
    Route::get('/rekap-laporan', [RekapLaporanController::class, 'index'])->name('rekap-laporan.index');
    Route::post('/rekap-laporan/export', [RekapLaporanController::class, 'export'])->name('rekap-laporan.export');
    Route::get('/rekap-hasil', [RekapHasilController::class, 'index'])->name('rekap-hasil.index');
    Route::get('/layar-nominasi', [LayarNominasiController::class, 'index'])->name('layar-nominasi.index');

    // Sistem - Hanya Super Admin
    Route::middleware('auth')->group(function () {
        Route::middleware('can:is-super-admin')->group(function () {
            Route::resource('admin-management', AdminManagementController::class)->parameters([
                'admin-management' => 'admin'
            ]);
            Route::get('/pengaturan-event', [PengaturanEventController::class, 'index'])->name('pengaturan-event.index');
            Route::put('/pengaturan-event', [PengaturanEventController::class, 'update'])->name('pengaturan-event.update');
        });
    });
});
