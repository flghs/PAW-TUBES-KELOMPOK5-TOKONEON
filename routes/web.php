<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\GalleryItemController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\CustomPageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderProofController;
use App\Http\Controllers\Staff\MaterialController as StaffMaterialController;
use App\Http\Controllers\Staff\MaterialStockController;
use App\Http\Controllers\Staff\OrderController as StaffOrderController;
use App\Http\Middleware\EnsureUserHasRole;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Halaman publik (tanpa login).
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/products/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/custom', [CustomPageController::class, 'index'])->name('custom.index');

// Beli sekarang dan pesanan pembeli. Pembeli tidak punya akun:
// pesanan dibuka lewat sesi browser, atau lewat nomor pesanan + nomor telepon.
Route::get('/checkout/{product:slug}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{product:slug}', [CheckoutController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('checkout.store');

Route::get('/track', [CustomerOrderController::class, 'track'])->name('orders.track');
Route::post('/track', [CustomerOrderController::class, 'lookup'])
    ->middleware('throttle:10,1')
    ->name('orders.lookup');

Route::get('/orders/{order:order_number}', [CustomerOrderController::class, 'show'])->name('orders.show');
Route::post('/orders/{order:order_number}/cancel', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');
Route::post('/orders/{order:order_number}/payment-proof', [CustomerOrderController::class, 'uploadProof'])
    ->middleware('throttle:10,1')
    ->name('orders.proof.store');

// Bukti bayar (pemeriksaan hak akses dilakukan di dalam controller).
Route::get('/orders/{order:order_number}/proof', OrderProofController::class)->name('orders.proof');

// Login hanya untuk staff dan admin, dan hanya untuk yang belum login.
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

// Semua staff dan admin yang sudah login.
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
        Route::resource('gallery', GalleryItemController::class)->except('show');

        // Data bahan baku: tambah dan ubah (tidak ada hapus, hanya dinonaktifkan).
        Route::resource('materials', AdminMaterialController::class)->only(['create', 'store', 'edit', 'update']);
    });

// Staff dan admin.
Route::middleware(['auth', EnsureUserHasRole::class . ':' . User::ROLE_STAFF . ',' . User::ROLE_ADMIN])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::view('/', 'staff.dashboard')->name('dashboard');

        Route::get('/orders', [StaffOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [StaffOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/payment/approve', [StaffOrderController::class, 'approvePayment'])->name('orders.payment.approve');
        Route::post('/orders/{order}/payment/reject', [StaffOrderController::class, 'rejectPayment'])->name('orders.payment.reject');
        Route::patch('/orders/{order}/status', [StaffOrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{order}/cancel', [StaffOrderController::class, 'cancel'])->name('orders.cancel');

        // Bahan baku: lihat dan catat stok.
        Route::get('/materials', [StaffMaterialController::class, 'index'])->name('materials.index');
        Route::get('/materials/{material}', [StaffMaterialController::class, 'show'])->name('materials.show');
        Route::post('/materials/{material}/stock', [MaterialStockController::class, 'store'])->name('materials.stock.store');
    });
