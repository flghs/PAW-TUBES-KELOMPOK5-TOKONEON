<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Customer\CustomRequestController as CustomerCustomRequestController;
use App\Http\Controllers\CustomRequestImageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Staff\CustomRequestController as StaffCustomRequestController;
use App\Http\Middleware\EnsureUserHasRole;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Halaman publik (tanpa login).
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/products/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');

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

    // Gambar referensi (pemeriksaan hak akses dilakukan di dalam controller).
    Route::get('/custom-requests/{customRequest}/image', CustomRequestImageController::class)
        ->name('custom-requests.image');
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
        Route::get('/custom-requests', [StaffCustomRequestController::class, 'index'])->name('custom-requests.index');
        Route::get('/custom-requests/{customRequest}', [StaffCustomRequestController::class, 'show'])->name('custom-requests.show');
    });

// Hanya pelanggan.
Route::middleware(['auth', EnsureUserHasRole::class . ':' . User::ROLE_CUSTOMER])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::view('/', 'customer.dashboard')->name('dashboard');
        Route::resource('custom-requests', CustomerCustomRequestController::class)
            ->only(['index', 'create', 'store', 'show']);
    });
