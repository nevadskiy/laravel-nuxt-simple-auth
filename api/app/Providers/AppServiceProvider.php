<?php

namespace App\Providers;

use App\Services\Auth\ApiTokenGenerator;
use App\Services\Auth\RandomTokenGenerator;
use App\UseCases\Auth\SignIn\Handler;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(ApiTokenGenerator::class, RandomTokenGenerator::class);

        $this->app->bind(Handler::class, function (Application $app) {
            return new Handler(Auth::guard()->getProvider(), $app[ApiTokenGenerator::class]);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
