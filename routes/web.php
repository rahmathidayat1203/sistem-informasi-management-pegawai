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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PegawaiProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Reports routes (authorized users)
    Route::middleware(['permission:view pegawai'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/duk', [ReportController::class, 'duk'])->name('reports.duk');
        Route::get('/reports/rekap-pegawai', [ReportController::class, 'rekapPegawai'])->name('reports.rekap-pegawai');
        Route::get('/reports/statistik-cuti', [ReportController::class, 'statistikCuti'])->name('reports.statistik-cuti');
        Route::get('/reports/laporan-perjalanan-dinas', [ReportController::class, 'laporanPerjalananDinas'])->name('reports.laporan-perjalanan-dinas');
        Route::get('/reports/duk/pdf', [ReportController::class, 'exportDukPdf'])->name('reports.duk.pdf');
        Route::get('/reports/duk/excel', [ReportController::class, 'exportDukExcel'])->name('reports.duk.excel');
    });

    // Admin Routes with RBAC protection
    Route::prefix('admin')->name('admin.')->middleware(['role:Admin Kepegawaian|Pimpinan|Admin Keuangan|Pegawai'])->group(function () {
        
        // Routes for Admin Kepegawaian only
        Route::middleware(['role:Admin Kepegawaian'])->group(function () {
            Route::resource('pegawai', PegawaiController::class);
            Route::resource('jabatan', JabatanController::class);
            Route::resource('golongan', GolonganController::class);
            Route::resource('unit_kerja', UnitKerjaController::class);
            Route::resource('jenis_cuti', JenisCutiController::class);
            Route::resource('sisa_cuti', SisaCutiController::class);
            Route::get('sisa_cuti/export/pdf', [SisaCutiController::class, 'exportPdf'])->name('sisa_cuti.export.pdf');
            Route::resource('roles', RoleController::class);
            Route::resource('permissions', PermissionController::class);
            Route::get('roles/{id}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
            Route::put('roles/{id}/assign-permissions', [RoleController::class, 'assignPermissions'])->name('roles.assignPermissions');
            Route::resource('users', UserController::class);
        });

        // Routes that require specific permissions
        Route::middleware(['permission:view pendidikan'])->group(function () {
            Route::resource('pendidikan', PendidikanController::class);
        });

        Route::middleware(['permission:view keluarga'])->group(function () {
            Route::resource('keluarga', KeluargaController::class);
        });

        Route::middleware(['permission:view riwayat_pangkat'])->group(function () {
            Route::resource('riwayat_pangkat', RiwayatPangkatController::class);
        });

        Route::middleware(['permission:view riwayat_jabatan'])->group(function () {
            Route::resource('riwayat_jabatan', RiwayatJabatanController::class);
        });

        // Cuti routes with permission checks
        Route::middleware(['permission:view cuti'])->group(function () {
            Route::get('cuti/sisa-cuti/{pegawai}', [CutiController::class, 'sisaCuti'])->name('cuti.sisa-cuti');
            Route::resource('cuti', CutiController::class);
            Route::get('cuti/export/pdf', [CutiController::class, 'exportPdf'])->name('cuti.export.pdf');
        });

        // Perjalanan Dinas routes
        Route::middleware(['permission:view perjalanan_dinas'])->group(function () {
            Route::resource('perjalanan_dinas', PerjalananDinasController::class);
            Route::get('pegawai/search', [PerjalananDinasController::class, 'searchPegawai'])->name('perjalanan_dinas.searchpegawai');
            Route::get('perjalanan_dinas/export/pdf', [PerjalananDinasController::class, 'exportPdf'])->name('perjalanan_dinas.export.pdf');
        });

        // Laporan PD routes
        Route::middleware(['permission:view laporan_pd'])->group(function () {
            Route::resource('laporan_pd', LaporanPDController::class);
            Route::post('laporan_pd/{laporanPD}/verify', [LaporanPDController::class, 'verify'])->name('laporan_pd.verify');
            Route::post('laporan_pd/{laporanPD}/reject', [LaporanPDController::class, 'reject'])->name('laporan_pd.reject');
        });
    });

    // Pimpinan specific routes
    Route::middleware(['role:Pimpinan'])->prefix('pimpinan')->name('pimpinan.')->group(function () {
        Route::get('cuti/approval', [CutiController::class, 'approvalIndex'])->name('cuti.approval');
        Route::post('cuti/{cuti}/approve', [CutiController::class, 'approve'])->name('cuti.approve');
        Route::post('cuti/{cuti}/reject', [CutiController::class, 'reject'])->name('cuti.reject');
        
        Route::get('perjalanan-dinas/assignments', [PerjalananDinasController::class, 'assignmentIndex'])->name('perjalanan_dinas.assignment');
        Route::post('perjalanan-dinas/{perjalananDinas}/assign', [PerjalananDinasController::class, 'assign'])->name('perjalanan_dinas.assign');
    });

    // Admin Keuangan specific routes
    Route::middleware(['role:Admin Keuangan'])->prefix('keuangan')->name('keuangan.')->group(function () {
        Route::get('laporan-pd/verification', [LaporanPDController::class, 'verificationIndex'])->name('laporan_pd.verification');
        Route::get('laporan-pd/verification-stats', [LaporanPDController::class, 'verificationStats'])->name('laporan_pd.verificationStats');
        Route::post('laporan-pd/{laporanPd}/verify', [LaporanPDController::class, 'verify'])->name('laporan_pd.verify');
        Route::post('laporan-pd/{laporanPd}/reject', [LaporanPDController::class, 'reject'])->name('laporan_pd.reject');
    });

    // Pegawai specific routes
    Route::middleware(['role:Pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {
        Route::get('profile', [PegawaiProfileController::class, 'index'])->name('profile');
        Route::get('profile/edit', [PegawaiProfileController::class, 'editPersonalData'])->name('profile.edit');
        Route::patch('profile', [PegawaiProfileController::class, 'updatePersonalData'])->name('profile.update');
        Route::post('pendidikan', [PegawaiProfileController::class, 'storePendidikan'])->name('pendidikan.store');
        Route::post('keluarga', [PegawaiProfileController::class, 'storeKeluarga'])->name('keluarga.store');
        Route::get('statistics', [PegawaiProfileController::class, 'statistics'])->name('statistics');
        Route::get('download/{type}/{id}', [PegawaiProfileController::class, 'downloadDocument'])->name('download.document');
        Route::get('cuti/my-cutis', [CutiController::class, 'myCutis'])->name('cuti.my');
        Route::get('perjalanan-dinas/my-assignments', [PerjalananDinasController::class, 'myAssignments'])->name('perjalanan_dinas.my');
        Route::get('laporan-pd/my-reports', [LaporanPDController::class, 'myReports'])->name('laporan_pd.my');
    });
});

require __DIR__.'/auth.php';