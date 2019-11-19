<?php

namespace App\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootMigrations();
        $this->bootRoutes();
    }

    /**
     * Boot any module migrations.
     */
    private function bootMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }

    /**
     * Boot any module routes.
     */
    private function bootRoutes(): void
    {
        $this->app['router']->group([
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/Http/Routes/web.php');
        });
    }
}
