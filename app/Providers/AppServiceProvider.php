<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use MongoDB\Laravel\Auth\AccessToken as PersonalAccessToken;

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
        // السطر ده هو اللي بيربط Sanctum بالمونجو صح
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}