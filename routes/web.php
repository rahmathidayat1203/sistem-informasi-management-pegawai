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
use App\Http\Controllers\Admin\SisaCutiController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController;
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
        Route::resource('sisa_cuti', SisaCutiController::class); // Tambahkan route untuk sisa cuti
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::get('roles/{id}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::put('roles/{id}/assign-permissions', [RoleController::class, 'assignPermissions'])->name('roles.assignPermissions');
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';