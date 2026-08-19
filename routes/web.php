<?php

use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\DashboardController;
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

Route::get('/categories', [AssetCategoryController::class, 'webIndex'])
    ->middleware(['auth', 'verified'])
    ->name('categories.index');

Route::get('/borrowings/mine', [BorrowingController::class, 'webMine'])
    ->middleware(['auth', 'verified'])
    ->name('borrowings.mine');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';