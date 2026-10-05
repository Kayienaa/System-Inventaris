<?php

use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiPintuController;
use App\Http\Controllers\UserGuideController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Integrasi SiPintu Gateway: SSO Otomatis & Webhook Sinkronisasi Real-time
|--------------------------------------------------------------------------
*/
Route::get('/oauth/callback', [OAuthController::class, 'callback'])->name('oauth.callback');

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => 'SITEFA',
        'timestamp' => now()->toIsoString(),
    ], 200);
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])
    ->middleware(['auth', 'verified', 'role:admin|super_admin'])
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

    Route::post('/borrowings/{borrowing}/checkout', [BorrowingController::class, 'webCheckout'])
        ->name('borrowings.checkout');

    Route::post('/borrowings/{borrowing}/return-request', [BorrowingController::class, 'requestReturn'])
        ->name('borrowings.return-request');

    Route::post('/peminjaman/{borrowing}/cancel', [BorrowingController::class, 'webCancel'])
        ->name('borrowings.cancel');
    Route::post('/borrowings/{borrowing}/cancel', [BorrowingController::class, 'webCancel']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/admin-whatsapp', [ProfileController::class, 'updateAdminWhatsApp'])
        ->name('profile.admin-whatsapp.update')
        ->middleware(['role:admin|super_admin']);
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Halaman Panduan Peminjaman untuk Peminjam (Siswa & Guru)
    Route::get('/panduan', [UserGuideController::class, 'index'])
        ->middleware(['role:siswa|guru'])
        ->name('guides.user');

    // Shortcut Cepat Super Admin di Akun Pak Agung
    Route::post('/switch-to-super-admin', [\App\Http\Controllers\Auth\SuperAdminSwitchController::class, 'switchToSuperAdmin'])
        ->name('switch-to-super-admin');
});

/*
|--------------------------------------------------------------------------
| Panel Admin Operasional & Super Admin — Master Aset & Monitoring Peminjaman
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin|super_admin'])->group(function () {
    Route::resource('admin/assets', \App\Http\Controllers\Admin\AssetManagementController::class)->names('admin.assets');
});

Route::middleware(['auth', 'role:admin|super_admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard/weekly-history', [DashboardController::class, 'weeklyHistory'])->name('admin.dashboard.weekly-history');
    Route::get('/borrowings', [\App\Http\Controllers\Admin\BorrowingController::class, 'index'])->name('admin.borrowings.index');
    Route::get('/borrowings/export-excel', [\App\Http\Controllers\Admin\BorrowingReportController::class, 'exportCsv'])->name('admin.borrowings.export-excel');
    Route::get('/borrowings/export-pdf', [\App\Http\Controllers\Admin\BorrowingReportController::class, 'exportPdf'])->name('admin.borrowings.export-pdf');
    Route::get('/borrowings/{borrowing}', [\App\Http\Controllers\Admin\BorrowingController::class, 'show'])->whereNumber('borrowing')->name('admin.borrowings.show');

    // Aksi Persetujuan & Verifikasi Fisik oleh Admin / Super Admin
    Route::post('/borrowings/{borrowing}/checkout', [BorrowingController::class, 'webCheckout'])->name('admin.borrowings.checkout');
    Route::post('/borrowings/{borrowing}/approve', [BorrowingController::class, 'webApprove'])->name('admin.borrowings.approve');
    Route::post('/borrowings/{borrowing}/reject', [BorrowingController::class, 'webReject'])->name('admin.borrowings.reject');
    Route::post('/borrowings/{borrowing}/verify-return', [BorrowingController::class, 'webVerifyReturn'])->name('admin.borrowings.verify-return');

    // Notifikasi Real-Time In-App Admin & Super Admin
    Route::get('/notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-read');
    Route::delete('/notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'destroyAll'])->name('admin.notifications.destroy-all');

    // Halaman Panduan SOP Operasional Admin & Super Admin
    Route::get('/guides', [\App\Http\Controllers\Admin\GuideController::class, 'index'])->name('admin.guides.index');
});

/*
|--------------------------------------------------------------------------
| Panel Eksklusif Super Admin — Audit Log & Sinkronisasi Gateway SiPintu
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->group(function () {
    Route::get('/audit-logs', [\App\Http\Controllers\AuditLogController::class, 'webIndex'])->name('admin.audit-logs.index');
    Route::get('/audit-logs/export-excel', [\App\Http\Controllers\Admin\BorrowingReportController::class, 'exportCsv'])->name('admin.audit-logs.export-excel');
    Route::get('/audit-logs/export-pdf', [\App\Http\Controllers\Admin\BorrowingReportController::class, 'exportPdf'])->name('admin.audit-logs.export-pdf');
    Route::post('/sync-sipintu', [\App\Http\Controllers\Admin\SiPintuSyncController::class, 'sync'])->name('admin.sync-sipintu');
});

Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('sipintu')->group(function () {
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
    abort_if(str_contains($path, '..'), 400);

    // M5: Subfolder bukti foto peminjaman/pengembalian bersifat semi-private —
    // hanya dapat diakses oleh pengguna yang sudah login.
    if (str_starts_with($path, 'borrowing-evidence/')
        || str_starts_with($path, 'return-evidence/')) {
        abort_unless(auth()->check(), 401);
    }

    $disk = Storage::disk('public');
    abort_unless($disk->exists($path), 404);

    // Cache-Control private untuk evidence, public untuk aset/foto lainnya
    $cacheControl = (str_starts_with($path, 'borrowing-evidence/') || str_starts_with($path, 'return-evidence/'))
        ? 'private, max-age=3600'
        : 'public, max-age=86400';

    return response()->file($disk->path($path), [
        'Cache-Control' => $cacheControl,
    ]);
})->where('path', '.*')->name('storage.local');

require __DIR__.'/auth.php';