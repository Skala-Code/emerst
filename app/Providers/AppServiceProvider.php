<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentView;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        FilamentView::registerRenderHook(
            'panels::head.start',
            fn (): string => '<meta name="robots" content="noindex,nofollow">'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS URLs in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Configure Livewire for production
        if (config('app.env') === 'production') {
            Livewire::setUpdateRoute(function ($handle) {
                return Route::post('/livewire/update', $handle);
            });

            Livewire::setScriptRoute(function ($handle) {
                return Route::get('/livewire/livewire.js', $handle);
            });
        }
    }
}
