<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminItemController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\DocumentController; // 🌟 INI TAMBAHAN BARU UNTUK PDF
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Role-Based Dashboard Redirect
|--------------------------------------------------------------------------
| This is the "Traffic Controller". It sends Admins to their management
| suite and Students to the rental catalog.
*/
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Requires Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // --- Profile Management ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |----------------------------------------------------------------------
    | 🛡️ ADMIN SECTION
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        
        Route::get('/dashboard', [AdminItemController::class, 'index'])->name('dashboard');

        Route::get('/items', [AdminItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [AdminItemController::class, 'create'])->name('items.create');
        Route::post('/items', [AdminItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [AdminItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [AdminItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [AdminItemController::class, 'destroy'])->name('items.destroy');

        Route::get('/orders', [AdminItemController::class, 'orders'])->name('orders');
        Route::patch('/orders/{order}/status', [AdminItemController::class, 'updateStatus'])->name('orders.update');  
        
        Route::get('/orders/export', [AdminItemController::class, 'exportExcel'])->name('orders.export');
    });

    /*
    |----------------------------------------------------------------------
    | 👤 STUDENT SECTION
    |----------------------------------------------------------------------
    */
    Route::prefix('student')->name('student.')->group(function () {
        
        // 1. Catalog & Personal History
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/loans', [UserDashboardController::class, 'loans'])->name('loans');
        
        // 2. 🛒 The New Enterprise Cart System
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'viewCart'])->name('index');
            Route::post('/add/{item}', [CartController::class, 'addToCart'])->name('add');
            Route::post('/clear', [CartController::class, 'clearCart'])->name('clear');
            Route::post('/checkout', [CartController::class, 'processCheckout'])->name('checkout');
        });

        // 3. 📄 PDF & Document Generator (RUANG CETAK SURAT)
        Route::prefix('document')->name('document.')->group(function () {
            Route::get('/mou/{order}', [DocumentController::class, 'downloadMou'])->name('mou');
            Route::get('/invoice/{order}', [DocumentController::class, 'downloadInvoice'])->name('invoice');
        });
    });
});

require __DIR__.'/auth.php';