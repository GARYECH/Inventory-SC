<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminItemController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\RentalController;
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
        
        // Gunakan satu pintu masuk utama untuk dashboard
        Route::get('/dashboard', [AdminItemController::class, 'index'])->name('dashboard');

        // Explicitly define inventory routes instead of resource (agar gampang di-click)
        Route::get('/items', [AdminItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [AdminItemController::class, 'create'])->name('items.create');
        Route::post('/items', [AdminItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}/edit', [AdminItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [AdminItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [AdminItemController::class, 'destroy'])->name('items.destroy');

        // Order & Approval Logic
        Route::get('/orders', [AdminItemController::class, 'orders'])->name('orders');
        Route::patch('/orders/{order}/status', [AdminItemController::class, 'updateStatus'])->name('orders.update');  
        
        // Export Feature
        Route::get('/orders/export', [AdminItemController::class, 'exportExcel'])->name('orders.export');
    });

    /*
    |----------------------------------------------------------------------
    | 👤 STUDENT SECTION
    |----------------------------------------------------------------------
    | All routes here are prefixed with /student and named student.something
    */
    Route::prefix('student')->name('student.')->group(function () {
        
        // 1. Catalog & Personal History
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/loans', [UserDashboardController::class, 'loans'])->name('loans');
        
        // 2. The Elite Booking Flow
        // create: Shows the transparency/booking page
        // store: Processes the reservation with date validation
        Route::get('/rent/{item}', [RentalController::class, 'create'])->name('rent.create');
        Route::post('/rent/{item}', [RentalController::class, 'store'])->name('rent.store');

        Route::patch('/rent/cancel/{order}', [RentalController::class, 'cancel'])->name('rent.cancel');
    });
});

require __DIR__.'/auth.php';