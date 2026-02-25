<?php

namespace App\Providers;

use App\Auth\FirebaseUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Contract\Database;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::provider('firebase', function ($app, array $config) {
            return new FirebaseUserProvider($app->make(Database::class));
        });
    }
}
