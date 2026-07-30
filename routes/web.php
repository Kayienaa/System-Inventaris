<?php

use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Hanya Guru & Siswa — melihat katalog & meminjam alat
Route::middleware(['auth', 'role:guru,siswa'])->group(function () {
    Route::get('/katalog', [ItemController::class, 'index'])->name('items.index');
    Route::post('/borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');
    Route::get('/borrowings/saya', [BorrowingController::class, 'myBorrowings'])->name('borrowings.mine');
});

// Hanya Admin — kelola master data & analytics
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('items', ItemController::class)->except(['index']);
    Route::resource('categories', CategoryController::class);
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
});

require __DIR__.'/auth.php';