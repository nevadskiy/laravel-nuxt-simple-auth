<?php

namespace App\Providers;

use App\Services\Auth\TokenGenerator;
use App\UseCases\Auth\SignIn\Handler;
use App\Services\Url\Link;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Foundation\Application;
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
        $this->app->bind(TokenGenerator\ApiTokenGenerator::class, TokenGenerator\RandomTokenGenerator::class);

        $this->app->when(Handler::class)
            ->needs(UserProvider::class)
            ->give(function (Application $app) {
                return $app['auth']->guard()->getProvider();
            });

        $this->app->when(Link::class)
            ->needs('$baseUrl')
            ->give(function (Application $app) {
                return $app['config']['app']['url'];
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
