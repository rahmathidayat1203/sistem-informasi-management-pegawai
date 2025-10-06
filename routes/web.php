<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\GolonganController;
use App\Http\Controllers\Admin\UnitKerjaController;
use App\Http\Controllers\Admin\PendidikanController;
use App\Http\Controllers\Admin\KeluargaController;
use App\Http\Controllers\Admin\RiwayatPangkatController;
use App\Http\Controllers\Admin\RiwayatJabatanController;
use App\Http\Controllers\Admin\JenisCutiController;
use App\Http\Controllers\Admin\CutiController;
use App\Http\Controllers\Admin\PerjalananDinasController;
use App\Http\Controllers\Admin\LaporanPDController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('pegawai', PegawaiController::class);
        Route::resource('jabatan', JabatanController::class);
        Route::resource('golongan', GolonganController::class);
        Route::resource('unit_kerja', UnitKerjaController::class);
        Route::resource('pendidikan', PendidikanController::class);
        Route::resource('keluarga', KeluargaController::class);
        Route::resource('riwayat_pangkat', RiwayatPangkatController::class);
        Route::resource('riwayat_jabatan', RiwayatJabatanController::class);
        Route::resource('jenis_cuti', JenisCutiController::class);
        Route::resource('cuti', CutiController::class);
        Route::resource('perjalanan_dinas', PerjalananDinasController::class);
        Route::resource('laporan_pd', LaporanPDController::class);
    });
});

require __DIR__.'/auth.php';