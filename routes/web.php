<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;

// ── Health check — monitoring DB response time ──────────────
Route::get('/health', function () {
    $start  = microtime(true);
    DB::select('SELECT 1');
    $dbTime = round((microtime(true) - $start) * 1000, 2);

    return response()->json([
        'status'    => 'ok',
        'db_ms'     => $dbTime,
        'timestamp' => now()->toISOString(),
    ]);
});

// ── Redirect root ke dashboard ─────────────────────────────
Route::get('/', fn() => redirect()->route('dashboard'));

// ── Auth routes (bawaan Breeze) ────────────────────────────
require __DIR__.'/auth.php';

// ── Protected routes (semua harus login) ──────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ── POS — kasir, admin, owner ──────────────────────────
    Route::middleware(['can:pos.access'])->prefix('pos')->name('pos.')->group(function () {
        Route::get('/',                [PosController::class, 'index'])  ->name('index');
        Route::post('/',               [PosController::class, 'store'])  ->name('store')->middleware('throttle:30,1');
        Route::get('/receipt/{transaction}', [PosController::class, 'receipt'])->name('receipt');
        Route::get('/receipt/{transaction}/raw', [PosController::class, 'rawReceipt'])->name('receipt.raw');
        Route::post('/qris/generate',  [PosController::class, 'generateQris'])->name('qris.generate');
    });

    // ── Produk — siapa saja yang punya product.view ────────
    Route::middleware(['can:product.view'])->group(function () {
        Route::resource('products',   ProductController::class);
        Route::resource('categories', CategoryController::class);

        // Export & Import Harga Produk
        Route::get('products/export-prices/excel', [ProductController::class, 'exportPrices'])
            ->name('products.export-prices');
        Route::post('products/import-prices/excel', [ProductController::class, 'importPrices'])
            ->name('products.import-prices')
            ->middleware('can:product.edit');

        // Stok opname (butuh product.edit)
        Route::post('products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])
            ->name('products.adjust-stock')
            ->middleware('can:product.edit');

        // ── Stok Opname ─────────────────────────────────────
        Route::middleware(['can:product.edit'])->prefix('stock-opnames')->name('stock-opnames.')->group(function () {
            Route::get('/',                         [StockOpnameController::class, 'index'])  ->name('index');
            Route::get('/create',                   [StockOpnameController::class, 'create']) ->name('create');
            Route::post('/',                        [StockOpnameController::class, 'store'])  ->name('store');
            Route::get('/{stock_opname}',           [StockOpnameController::class, 'show'])   ->name('show');
            Route::patch('/{stock_opname}/confirm', [StockOpnameController::class, 'confirm'])->name('confirm');
            Route::patch('/{stock_opname}/cancel',  [StockOpnameController::class, 'cancel']) ->name('cancel');
        });
    });

    // ── Pembelian & Supplier ───────────────────────────────
    Route::middleware(['can:purchase.create'])->group(function () {
        Route::resource('suppliers', SupplierController::class);
        Route::resource('purchases', PurchaseController::class);
        Route::patch('purchases/{purchase}/confirm', [PurchaseController::class, 'confirm'])
            ->name('purchases.confirm')
            ->middleware('can:purchase.confirm');
    });

    // ── Laporan ────────────────────────────────────────────
    Route::middleware(['can:report.view'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/',          [ReportController::class, 'index'])       ->name('index');
        Route::get('/daily',     [ReportController::class, 'daily'])       ->name('daily');
        Route::get('/monthly',   [ReportController::class, 'monthly'])     ->name('monthly');
        Route::get('/products',  [ReportController::class, 'products'])    ->name('products');
        Route::get('/export/pdf',   [ReportController::class, 'exportPdf'])  ->name('export.pdf')  ->middleware('can:report.export');
        Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel')->middleware('can:report.export');
    });

    // ── Manajemen User — owner only ────────────────────────
    Route::middleware(['can:user.manage'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // ── Notifikasi ───────────────────────────────────────────
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',            [NotificationController::class, 'index'])         ->name('index');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])    ->name('read');
        Route::post('/read-all',   [NotificationController::class, 'markAllAsRead']) ->name('read-all');
    });

});
