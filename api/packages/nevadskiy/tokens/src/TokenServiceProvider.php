<?php

namespace Nevadskiy\Tokens;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Nevadskiy\Tokens\Generator\RandomHashGenerator;

class TokenServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerConfig();
        $this->registerDependencies();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootMigrations();
        $this->bootPublishable();
    }

    /**
     * Register the application config.
     */
    private function registerConfig(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'tokens');
    }

    /**
     * Register any application dependencies.
     */
    private function registerDependencies(): void
    {
        $this->app->singleton(TokenManager::class);

        $this->app->singletonIf(RandomHashGenerator::class, function (Application $app) {
            return new RandomHashGenerator($app['config']['app']['key']);
        });
    }

    /**
     * Boot any application migrations.
     */
    private function bootMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }

    /**
     * Boot any application publishable resources.
     */
    private function bootPublishable(): void
    {
        $this->publishes([$this->getConfigPath() => config_path('tokens.php')], 'tokens-config');
    }

    /**
     * Get the application config file path.
     *
     * @return string
     */
    private function getConfigPath(): string
    {
        return __DIR__ . '/../config/tokens.php';
    }
}
