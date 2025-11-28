<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\SocialiteManager;

class SocialiteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $socialite = $this->app->make(Factory::class);

        $socialite->extend(
            'microsoft',
            function ($app) use ($socialite) {
                $config = $app['config']['services.microsoft'];
                return $socialite->buildProvider(
                    \Laravel\Socialite\Two\MicrosoftProvider::class,
                    $config
                );
            }
        );
    }
}

