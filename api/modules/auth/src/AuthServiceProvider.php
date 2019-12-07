<?php

namespace Module\Auth;

use Module\Auth\Http\Middleware;
use Module\Auth\UseCases\SignIn\Handler;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Nevadskiy\Tokens\TokenManager;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The module's name.
     */
    public const NAME = 'auth';

    /**
     * The module's route middleware.
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $middleware = [
        'auth' => Middleware\Authenticate::class,
        'guest' => Middleware\OnlyAuthenticated::class,
    ];

    /**
     * The event listener mappings for the module.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

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
        $this->bootFactories();
        $this->bootMiddlewareAliases();
        $this->bootRoutes();
        $this->bootEvents();
        $this->bootTranslations();
        $this->bootTokens();
    }

    /**
     * Register the module configuration.
     */
    private function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/auth.php', self::NAME);
    }

    /**
     * Register any module dependencies.
     */
    private function registerDependencies(): void
    {
        $this->app->when(Handler::class)
            ->needs(UserProvider::class)
            ->give(function (Application $app) {
                return $app['auth']->guard()->getProvider();
            });
    }

    /**
     * Boot any module migrations.
     */
    private function bootMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Boot any module factories.
     */
    private function bootFactories(): void
    {
        if ($this->app->runningInConsole()) {
            $this->app[Factory::class]->load(__DIR__ . '/../database/factories');
        }
    }

    /**
     * Bootstrap any module middleware aliases.
     */
    private function bootMiddlewareAliases(): void
    {
        foreach ($this->middleware as $alias => $middleware) {
            $this->app['router']->aliasMiddleware($alias, $middleware);
        }
    }

    /**
     * Boot any module routes.
     */
    private function bootRoutes(): void
    {
        $this->app['router']->group([
            'prefix' => 'api',
            'middleware' => 'api',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }

    /**
     * Boot any module events.
     */
    private function bootEvents(): void
    {
        $dispatcher = $this->app[Dispatcher::class];

        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $dispatcher->listen($event, $listener);
            }
        }
    }

    /**
     * Boot any module translations.
     */
    private function bootTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', self::NAME);
    }

    /**
     * Boot any module tokens.
     */
    private function bootTokens(): void
    {
        $this->app[TokenManager::class]->define('password.reset', [
            'ttl' => 60,
            'previous' => 'reuse',
            'generation_throttling' => true,
            'generation_attempts' => 3,
            'generation_attempts_interval' => 10,
            'usage_throttling' => true,
            'usage_attempts' => 5,
            'usage_attempts_interval' => 10,
        ]);
    }
}
