<?php

use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiPintuController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('dashboard.analytics');

Route::get('/katalog', [AssetController::class, 'webIndex'])
    ->middleware(['auth', 'verified'])
    ->name('assets.index');

Route::get('/assets', [AssetController::class, 'webIndex'])
    ->middleware(['auth', 'verified']);

Route::get('/categories', [AssetCategoryController::class, 'webIndex'])
    ->middleware(['auth', 'verified'])
    ->name('categories.index');

Route::middleware('auth')->group(function () {
    Route::get('/katalog/{asset}/pinjam', [BorrowingController::class, 'create'])->name('assets.borrow');
    Route::post('/katalog/{asset}/pinjam', [BorrowingController::class, 'store'])->name('assets.borrow.store');

    Route::get('/peminjaman/riwayat', [BorrowingController::class, 'webMine'])->name('borrowings.mine');
    Route::get('/borrowings/mine', [BorrowingController::class, 'webMine']);

    Route::post('/borrowings/{borrowing}/return-request', [BorrowingController::class, 'requestReturn'])
        ->name('borrowings.return-request');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Panel Super Admin — Manajemen Master Aset, Audit Logs & Ekspor Laporan
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::resource('admin/assets', \App\Http\Controllers\Admin\AssetManagementController::class)->names('admin.assets');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/borrowings', [\App\Http\Controllers\Admin\BorrowingController::class, 'index'])->name('admin.borrowings.index');
    Route::get('/borrowings/export-excel', [\App\Http\Controllers\Admin\BorrowingReportController::class, 'exportCsv'])->name('admin.borrowings.export-excel');
    Route::get('/borrowings/export-pdf', [\App\Http\Controllers\Admin\BorrowingReportController::class, 'exportPdf'])->name('admin.borrowings.export-pdf');
    Route::get('/borrowings/{borrowing}', [\App\Http\Controllers\Admin\BorrowingController::class, 'show'])->whereNumber('borrowing')->name('admin.borrowings.show');
    Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'webIndex'])->name('admin.audit-logs.index');
    Route::get('/audit-logs/export-excel', [\App\Http\Controllers\Admin\BorrowingReportController::class, 'exportCsv'])->name('admin.audit-logs.export-excel');
    Route::get('/audit-logs/export-pdf', [\App\Http\Controllers\Admin\BorrowingReportController::class, 'exportPdf'])->name('admin.audit-logs.export-pdf');
    Route::post('/sync-sipintu', [\App\Http\Controllers\Admin\SiPintuSyncController::class, 'sync'])->name('admin.sync-sipintu');
});

/*
|--------------------------------------------------------------------------
| SiPintu API Gateway — Data SIJUNA (Admin Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('sipintu')->group(function () {
    Route::get('/', [SiPintuController::class, 'index'])->name('sipintu.index');
    Route::get('/pengguna', [SiPintuController::class, 'studentsPage'])->name('sipintu.students.page');
    Route::get('/guru', [SiPintuController::class, 'teachersPage'])->name('sipintu.teachers.page');
    
    // AJAX endpoints
    Route::get('/api/students', [SiPintuController::class, 'students'])->name('sipintu.students');
    Route::get('/api/teachers', [SiPintuController::class, 'teachers'])->name('sipintu.teachers');
    Route::get('/api/status', [SiPintuController::class, 'connectionStatus'])->name('sipintu.status');
});

/*
|--------------------------------------------------------------------------
| Public Storage Caching Route (Cache-Control Header)
|--------------------------------------------------------------------------
*/
Route::get('/storage/{path}', function (string $path) {
    $disk = Storage::disk('public');
    abort_unless($disk->exists($path), 404);

    return response()->file($disk->path($path), [
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.local');

require __DIR__.'/auth.php';