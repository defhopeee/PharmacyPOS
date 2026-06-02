<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Owner\CategoryController;
use App\Http\Controllers\Owner\ProductController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Owner\SupplierController;
use App\Http\Controllers\Owner\UserController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

/*
| Public / SEO
*/
Route::get('/', [HomeController::class, 'landing'])->name('home');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');

/*
| Authentication
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/*
| Authenticated (any role)
*/
Route::middleware(['auth', 'role'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Sales list + receipts (scoped per role inside the controller).
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
});

/*
| Attendant + Owner: Point of Sale
*/
Route::middleware(['auth', 'role:attendant,owner'])->group(function () {
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
});

/*
| Owner only
*/
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::resource('products', ProductController::class)->except('show');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('suppliers', SupplierController::class)->except('show');
    Route::resource('users', UserController::class)->except('show');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
});
