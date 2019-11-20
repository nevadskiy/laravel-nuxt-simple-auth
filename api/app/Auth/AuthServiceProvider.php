<?php

namespace App\Auth;

use App\Auth\Http\Middleware;
use App\Auth\UseCases\SignIn\Handler;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The module's name.
     */
    public const MODULE = 'auth';

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
    }

    /**
     * Register the module configuration.
     */
    private function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/auth.php', self::MODULE);
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
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }

    /**
     * Boot any module factories.
     */
    private function bootFactories(): void
    {
        if ($this->app->runningInConsole()) {
            $this->app[Factory::class]->load(__DIR__ . '/Database/Factories');
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
            $this->loadRoutesFrom(__DIR__ . '/Http/Routes/api.php');
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
}