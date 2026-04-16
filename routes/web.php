<?php

use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\OrderController as ShopOrderController;
use App\Http\Controllers\Shop\LocationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\AntifraudController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Middleware\BlockBannedIp;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

// ============================================================
// PUBLIC SHOP ROUTES
// ============================================================
Route::middleware([BlockBannedIp::class])->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
    Route::get('/produits/{slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/checkout', [ShopOrderController::class, 'checkout'])->name('checkout');
    Route::post('/commandes', [ShopOrderController::class, 'store'])->name('orders.store');
    Route::get('/suivi/{orderNumber}', [ShopOrderController::class, 'track'])->name('orders.track');

    // API locations
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/wilayas', [LocationController::class, 'wilayas'])->name('wilayas');
        Route::get('/communes/{wilayaId}', [LocationController::class, 'communes'])->name('communes');
    });
});

// ============================================================
// ADMIN ROUTES (auth + is_admin)
// ============================================================
Route::middleware(['auth', IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Produits
    Route::get('/produits', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/produits/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/produits', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/produits/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/produits/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::post('/produits/{product}', [AdminProductController::class, 'update']);
    Route::delete('/produits/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/produits/{id}/restore', [AdminProductController::class, 'restore'])->name('products.restore');

    // Catégories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Commandes
    Route::get('/commandes', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/commandes/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/commandes/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::delete('/commandes/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

    // Antifraude
    Route::get('/antifraude', [AntifraudController::class, 'index'])->name('antifraud.index');
    Route::post('/antifraude/ban-ip', [AntifraudController::class, 'banIp'])->name('antifraud.ban-ip');
    Route::delete('/antifraude/ips/{bannedIp}', [AntifraudController::class, 'unbanIp'])->name('antifraud.unban-ip');
    Route::patch('/antifraude/orders/{order}/unflag', [AntifraudController::class, 'unflagOrder'])->name('antifraud.unflag');

    // Paramètres
    Route::get('/parametres', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/parametres', [SettingsController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
