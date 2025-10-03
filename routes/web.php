<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

// Redirect root to admin login
Route::get('/', function () {
    return redirect('/admin');
})->name('home');

// Protected document routes
Route::middleware(['auth'])->group(function () {
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/view', [DocumentController::class, 'view'])->name('documents.view');
});
