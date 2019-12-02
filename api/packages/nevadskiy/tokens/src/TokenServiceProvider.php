<?php

namespace Nevadskiy\Tokens;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Nevadskiy\Tokens\Generator\RandomHashGenerator;
use Nevadskiy\Tokens\RateLimiter;
use Nevadskiy\Tokens\RateLimiter\CacheRateLimiter;
use Nevadskiy\Tokens\Repository\TokenRepository;
use Nevadskiy\Tokens\Tokens\OptionsToken;

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
     * Register any application configurations.
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
        $this->app->singleton(TokenManager::class, function (Application $app) {
            $manager = new TokenManager($app[TokenRepository::class], $app[CacheRateLimiter::class]);

            foreach ($this->app['config']['tokens']['defined'] as $token => $options) {
                if (is_int($token)) {
                    $manager->define($options);
                } else {
                    $manager->define($token, $options);
                }
            }

            return $manager;
        });

        $this->app->singletonIf(RateLimiter\CacheRateLimiter::class, RateLimiter\CacheRateLimiter::class);

        $this->app->singletonIf(RandomHashGenerator::class, function (Application $app) {
            return new RandomHashGenerator($app['config']['app']['key']);
        });

        $this->app->when(OptionsToken::class)->needs('$defaults')->give(function (Application $app) {
            return $app['config']['tokens']['defaults'];
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
