<?php

use App\Http\Controllers\Admin\MouEditorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin/mou-editor')
    ->name('admin.mou-editor.')
    ->group(function () {
        Route::get('/', [MouEditorController::class, 'index'])->name('index');
        Route::get('/orders/{order}/documents/{documentId}/edit', [MouEditorController::class, 'edit'])->name('edit');
        Route::put('/orders/{order}/documents/{documentId}', [MouEditorController::class, 'save'])->name('save');
        Route::post('/orders/{order}/documents/{documentId}/preview', [MouEditorController::class, 'preview'])->name('preview');
        Route::post('/orders/{order}/documents/{documentId}/reissue-signed', [MouEditorController::class, 'reissueSigned'])->name('reissue-signed');
        Route::post('/orders/{order}/documents/{documentId}/reissue-signed-upload', [MouEditorController::class, 'reissueSignedUpload'])->name('reissue-signed-upload');
        Route::get('/orders/{order}/documents/{documentId}/versions/{versionId}/pdf', [MouEditorController::class, 'versionPdf'])->name('version-pdf');
        Route::post('/orders/{order}/documents/{documentId}/versions/{versionId}/restore', [MouEditorController::class, 'restore'])->name('restore');
    });
