<?php

use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemBorrowingController;
use App\Http\Controllers\ProfileController;
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

Route::get('/assets', [AssetController::class, 'webIndex'])
    ->middleware(['auth', 'verified'])
    ->name('assets.index');

Route::get('/katalog', [ItemController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('items.index');

Route::get('/katalog/{item}/pinjam', [ItemBorrowingController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('items.borrow');

Route::post('/katalog/{item}/pinjam', [ItemBorrowingController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('items.borrow.store');

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

require __DIR__.'/auth.php';