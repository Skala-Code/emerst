<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

// Redirect root to admin login
Route::get('/', function () {
    return redirect('/admin');
})->name('home');

// Microsoft OAuth routes (must be outside auth middleware for callback to work)
Route::get('/auth/microsoft', [\App\Http\Controllers\MicrosoftAuthController::class, 'redirect'])->name('microsoft.redirect');
Route::get('/auth/microsoft/callback', [\App\Http\Controllers\MicrosoftAuthController::class, 'callback'])->name('microsoft.callback');
// Route to handle Azure redirect to /auth (if configured that way in Azure Portal)
Route::get('/auth', [\App\Http\Controllers\MicrosoftAuthController::class, 'callback'])->name('microsoft.callback.alternative');

// Protected document routes
Route::middleware(['auth'])->group(function () {
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/view', [DocumentController::class, 'view'])->name('documents.view');
    
    // Microsoft OAuth disconnect (requires auth)
    Route::post('/auth/microsoft/disconnect', [\App\Http\Controllers\MicrosoftAuthController::class, 'disconnect'])->name('microsoft.disconnect');
});
