<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\EnsureUserHasRole;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Hanya untuk yang belum login.
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

// Semua yang sudah login.
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Hanya admin.
Route::middleware(['auth', EnsureUserHasRole::class . ':' . User::ROLE_ADMIN])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::view('/', 'admin.dashboard')->name('dashboard');
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('products', ProductController::class)->except('show');
    });

// Staff dan admin.
Route::middleware(['auth', EnsureUserHasRole::class . ':' . User::ROLE_STAFF . ',' . User::ROLE_ADMIN])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::view('/', 'staff.dashboard')->name('dashboard');
    });

// Hanya pelanggan.
Route::middleware(['auth', EnsureUserHasRole::class . ':' . User::ROLE_CUSTOMER])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::view('/', 'customer.dashboard')->name('dashboard');
    });
