## About
The module use the default laravel API guard with the simple api_token field in the users table.

Password broker is replaced with nevadskiy/tokens package. 

## Installation

- Add the module repository to composer.json
```
"repositories": [
    {
        "type": "path",
        "url": "modules/auth",
        "options": {
            "symlink": true
        }
    }
]
```

- Install as the composer package
```
composer require module/auth:*
```

- Remove `guest` and `auth` middleware from `$routeMiddleware` array in the `app/Http/Kernel.php` file.
They are loaded automatically from the `Auth` module.

- Replace `\App\Http\Middleware\Authenticate::class` with `\Module\Auth\Http\Middleware\Authenticate::class` 
in `$middlewarePriority` array in the in the `app/Http/Kernel.php` file.
