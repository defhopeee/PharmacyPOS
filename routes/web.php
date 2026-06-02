<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Owner\BackupController;
use App\Http\Controllers\Owner\CategoryController;
use App\Http\Controllers\Owner\ProductController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Owner\SupplierController;
use App\Http\Controllers\Owner\UserController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

/*
| Entry point — internal system, so login is the default page.
*/
Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
})->name('home');

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
    Route::post('/pos/mpesa', [PosController::class, 'mpesa'])->name('pos.mpesa');
    Route::get('/pos/mpesa/{checkoutid}', [PosController::class, 'mpesaStatus'])->name('pos.mpesa.status');
});

/*
| Owner only
*/
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    $only = ['index', 'store', 'update', 'destroy'];
    Route::resource('products', ProductController::class)->only($only);
    Route::resource('categories', CategoryController::class)->only($only);
    Route::resource('suppliers', SupplierController::class)->only($only);
    Route::resource('users', UserController::class)->only($only);
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');

    Route::get('backup', [BackupController::class, 'download'])->name('backup');
});
