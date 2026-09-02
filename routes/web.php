<?php

use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiPintuController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.analytics');

Route::get('/katalog', [AssetController::class, 'webIndex'])
    ->middleware(['auth', 'verified'])
    ->name('assets.index');

Route::get('/assets', [AssetController::class, 'webIndex'])
    ->middleware(['auth', 'verified']);

Route::get('/katalog/{asset}/pinjam', [BorrowingController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('assets.borrow');

Route::post('/katalog/{asset}/pinjam', [BorrowingController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('assets.borrow.store');

Route::get('/categories', [AssetCategoryController::class, 'webIndex'])
    ->middleware(['auth', 'verified'])
    ->name('categories.index');

Route::get('/borrowings/mine', [BorrowingController::class, 'webMine'])
    ->middleware(['auth', 'verified'])
    ->name('borrowings.mine');

Route::post('/borrowings/{borrowing}/return-request', [BorrowingController::class, 'requestReturn'])
    ->middleware(['auth', 'verified'])
    ->name('borrowings.return-request');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Panel Super Admin — Audit Logs
|--------------------------------------------------------------------------
*/
Route::get('/admin/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'webIndex'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.audit-logs.index');

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

require __DIR__.'/auth.php';