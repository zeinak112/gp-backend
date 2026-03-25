<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use MongoDB\Laravel\Auth\AccessToken as PersonalAccessToken; 
use Laravel\Sanctum\Sanctum;

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
        // السطر ده هو اللي هيحل مشكلة الـ prepare() on null للأبد
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}