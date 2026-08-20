<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminItemController;
use App\Http\Controllers\Admin\CategoryController; 
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\DocumentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

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

    // ==========================================================
    // 🔔 INBOX NOTIFIKASI (HALAMAN KHUSUS ALA GOJEK/GRAB)
    // ==========================================================
    Route::get('/notifications', function () {
        // 1. Otomatis ubah status semua notif jadi "Sudah dibaca"
        auth()->user()->unreadNotifications->markAsRead();
        // 2. Reset memori Laravel biar titik merah langsung hilang!
        auth()->user()->unsetRelation('unreadNotifications');
        
        return view('notifications.index');
    })->name('notifications.index');

    // 🌟 ROUTE UNTUK MENGHAPUS SEMUA NOTIFIKASI SEKALIGUS
    Route::post('/notifications/clear', function () {
        auth()->user()->notifications()->delete();
        return back()->with('success', 'Semua notifikasi telah dibersihkan!');
    })->name('notifications.clear');

    // 🌟 ROUTE UNTUK MENGHAPUS NOTIFIKASI SATU PER SATU (TONG SAMPAH)
    Route::delete('/notifications/{id}', function ($id) {
        auth()->user()->notifications()->where('id', $id)->delete();
        return back()->with('success', 'Notifikasi berhasil dihapus.');
    })->name('notifications.destroy');

    /*
    |----------------------------------------------------------------------
    | 🛡️ ADMIN SECTION
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        
        Route::get('/dashboard', [AdminItemController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);

        Route::get('/items', [AdminItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [AdminItemController::class, 'create'])->name('items.create');
        Route::post('/items', [AdminItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [AdminItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [AdminItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [AdminItemController::class, 'destroy'])->name('items.destroy');

        Route::get('/orders', [AdminItemController::class, 'orders'])->name('orders');
        Route::patch('/orders/{order}/status', [AdminItemController::class, 'updateStatus'])->name('orders.update');  
        Route::delete('/orders/{id}', [AdminItemController::class, 'destroyOrder'])->name('orders.destroy');

        // 🌟 PENGATURAN SISTEM & SOP
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        
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
        Route::get('/item/{id}/schedule', [UserDashboardController::class, 'itemSchedule'])->name('item.schedule');
        
        // 🌟 RUTE UPLOAD DOKUMEN & PENGEMBALIAN BARANG 🌟
        Route::post('/orders/{order}/upload-mou', [DocumentController::class, 'uploadSignedMou'])->name('orders.upload-mou');
        Route::post('/orders/{order}/upload-payment', [DocumentController::class, 'uploadPaymentReceipt'])->name('orders.upload-payment');
        Route::post('/orders/{order}/upload-kwitansi', [DocumentController::class, 'uploadSignedKwitansi'])->name('orders.upload-kwitansi');
        Route::post('/orders/{order}/return-link', [DocumentController::class, 'submitReturnLink'])->name('orders.return-link');
        Route::post('/orders/{order}/upload-ba', [DocumentController::class, 'uploadBeritaAcara'])->name('orders.upload-ba'); // 👈 Upload BA
        
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
            Route::get('/kwitansi/{order}', [DocumentController::class, 'downloadKwitansi'])->name('kwitansi');
            Route::get('/berita-acara/{order}', [DocumentController::class, 'downloadBeritaAcara'])->name('berita-acara'); // 👈 Download BA
        });
    });
});

require __DIR__.'/auth.php';