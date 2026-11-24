<?php

use App\Http\Controllers\Admin\BaEditorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin/ba-editor')
    ->name('admin.ba-editor.')
    ->group(function () {
        Route::get('/orders/{order}', [BaEditorController::class, 'edit'])->name('edit');
        Route::put('/orders/{order}', [BaEditorController::class, 'save'])->name('save');
    });
