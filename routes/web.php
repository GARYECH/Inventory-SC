<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminItemController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\DocumentController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Google Login
|--------------------------------------------------------------------------
*/

Route::get(
    'auth/google',
    [GoogleController::class, 'redirectToGoogle']
)->name('google.login');

Route::get(
    'auth/google/callback',
    [GoogleController::class, 'handleGoogleCallback']
);


/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/

Route::get(
    '/api/check-stock/{id}',
    [UserDashboardController::class, 'checkStock']
);


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

        return redirect()->route(
            'admin.dashboard'
        );

    }

    return redirect()->route(
        'student.dashboard'
    );

})->middleware([
    'auth',
    'verified'
])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Profile Management
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::get('/notifications', function () {

        auth()->user()
            ->unreadNotifications
            ->markAsRead();

        auth()->user()
            ->unsetRelation(
                'unreadNotifications'
            );

        return view(
            'notifications.index'
        );

    })->name('notifications.index');


    Route::post('/notifications/clear', function () {

        auth()->user()
            ->notifications()
            ->delete();

        return back()->with(
            'success',
            'Semua notifikasi telah dibersihkan!'
        );

    })->name('notifications.clear');


    Route::delete('/notifications/{id}', function ($id) {

        auth()->user()
            ->notifications()
            ->where('id', $id)
            ->delete();

        return back()->with(
            'success',
            'Notifikasi berhasil dihapus.'
        );

    })->name('notifications.destroy');


    /*
    |--------------------------------------------------------------------------
    | ADMIN SECTION
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {


            /*
             * Dashboard
             */

            Route::get(
                '/dashboard',
                [AdminItemController::class, 'index']
            )->name('dashboard');


            /*
             * Categories
             */

            Route::resource(
                'categories',
                CategoryController::class
            )->only([
                'index',
                'store',
                'destroy'
            ]);


            /*
             * Items
             */

            Route::get(
                '/items',
                [AdminItemController::class, 'index']
            )->name('items.index');

            Route::get(
                '/items/create',
                [AdminItemController::class, 'create']
            )->name('items.create');

            Route::post(
                '/items',
                [AdminItemController::class, 'store']
            )->name('items.store');

            Route::get(
                '/items/{item}/edit',
                [AdminItemController::class, 'edit']
            )->name('items.edit');

            Route::put(
                '/items/{item}',
                [AdminItemController::class, 'update']
            )->name('items.update');

            Route::delete(
                '/items/{item}',
                [AdminItemController::class, 'destroy']
            )->name('items.destroy');


            /*
             * Orders
             */

            Route::get(
                '/orders',
                [AdminItemController::class, 'orders']
            )->name('orders');

            Route::patch(
                '/orders/{order}/status',
                [AdminItemController::class, 'updateStatus']
            )->name('orders.update');

            Route::delete(
                '/orders/{id}',
                [AdminItemController::class, 'destroyOrder']
            )->name('orders.destroy');


            /*
             * Settings
             */

            Route::get(
                '/settings',
                [
                    \App\Http\Controllers\Admin\SettingController::class,
                    'index'
                ]
            )->name('settings.index');

            Route::post(
                '/settings',
                [
                    \App\Http\Controllers\Admin\SettingController::class,
                    'update'
                ]
            )->name('settings.update');


            /*
             * Export
             */

            Route::get(
                '/orders/export',
                [AdminItemController::class, 'exportExcel']
            )->name('orders.export');

        });


    /*
    |--------------------------------------------------------------------------
    | STUDENT SECTION
    |--------------------------------------------------------------------------
    */

    Route::prefix('student')
        ->name('student.')
        ->group(function () {


            /*
             * Catalog
             */

            Route::get(
                '/dashboard',
                [UserDashboardController::class, 'index']
            )->name('dashboard');


            /*
             * Transaction History
             */

            Route::get(
                '/loans',
                [UserDashboardController::class, 'loans']
            )->name('loans');


            /*
             * Item Booking Schedule
             */

            Route::get(
                '/item/{id}/schedule',
                [UserDashboardController::class, 'itemSchedule']
            )->name('item.schedule');


            /*
             * ==========================================================
             * DOCUMENT UPLOADS
             * ==========================================================
             */


            /*
             * Multiple MoU Support
             *
             * Examples:
             * /student/orders/15/upload-mou/baju
             * /student/orders/15/upload-mou/id_card
             * /student/orders/15/upload-mou/ht
             */

            Route::post(
                '/orders/{order}/upload-mou/{type?}',
                [DocumentController::class, 'uploadSignedMou']
            )->name('orders.upload-mou');


            /*
             * Payment
             */

            Route::post(
                '/orders/{order}/upload-payment',
                [DocumentController::class, 'uploadPaymentReceipt']
            )->name('orders.upload-payment');


            /*
             * Signed Kwitansi
             */

            Route::post(
                '/orders/{order}/upload-kwitansi',
                [DocumentController::class, 'uploadSignedKwitansi']
            )->name('orders.upload-kwitansi');


            /*
             * Return Evidence
             */

            Route::post(
                '/orders/{order}/return-link',
                [DocumentController::class, 'submitReturnLink']
            )->name('orders.return-link');


            /*
             * Berita Acara
             */

            Route::post(
                '/orders/{order}/upload-ba',
                [DocumentController::class, 'uploadBeritaAcara']
            )->name('orders.upload-ba');


            /*
             * ==========================================================
             * CART
             * ==========================================================
             */

            Route::prefix('cart')
                ->name('cart.')
                ->group(function () {


                    Route::get(
                        '/',
                        [CartController::class, 'viewCart']
                    )->name('index');


                    Route::post(
                        '/add/{item}',
                        [CartController::class, 'addToCart']
                    )->name('add');


                    Route::post(
                        '/clear',
                        [CartController::class, 'clearCart']
                    )->name('clear');


                    Route::post(
                        '/checkout',
                        [CartController::class, 'processCheckout']
                    )->name('checkout');


                    Route::patch(
                        '/{id}/update',
                        [CartController::class, 'updateCart']
                    )->name('update');


                    Route::delete(
                        '/{id}/remove',
                        [CartController::class, 'removeItem']
                    )->name('remove');

                });


            /*
             * ==========================================================
             * DOCUMENT GENERATOR
             * ==========================================================
             */

            Route::prefix('document')
                ->name('document.')
                ->group(function () {


                    /*
                     * Multiple MoU Support
                     *
                     * Examples:
                     *
                     * /student/document/mou/15/baju
                     * /student/document/mou/15/id_card
                     * /student/document/mou/15/ht
                     */

                    Route::get(
                        '/mou/{order}/{type?}',
                        [DocumentController::class, 'downloadMou']
                    )->name('mou');


                    /*
                     * Invoice
                     */

                    Route::get(
                        '/invoice/{order}',
                        [DocumentController::class, 'downloadInvoice']
                    )->name('invoice');


                    /*
                     * Kwitansi
                     */

                    Route::get(
                        '/kwitansi/{order}',
                        [DocumentController::class, 'downloadKwitansi']
                    )->name('kwitansi');


                    /*
                     * Berita Acara
                     */

                    Route::get(
                        '/berita-acara/{order}',
                        [DocumentController::class, 'downloadBeritaAcara']
                    )->name('berita-acara');

                });

        });

});


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';