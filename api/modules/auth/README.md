## About
The module uses the default Laravel API guard with the simple `api_token` field in the users table.

Password broker is replaced with the (nevadskiy/token)[https://github.com/nevadskiy/tokens] package. 

## Installation

- Add the module repository to the `composer.json` file.
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

- Remove `Authenticate.php` and `RedirectIfAuthenticated.php` files from `app/Http/Middleware` directory.

- Replace `\App\Http\Middleware\Authenticate::class` with `\Illuminate\Auth\Middleware\Authenticate::class,` 
in `$middlewarePriority` array in the in the `app/Http/Kernel.php` file.

 
#### TODO:
- [x] Refactor authToken plugin with services injection (find an article about that approach)
- [x] Rename non-base component with no App prefix
- [x] Add a nuxt guest middleware
- [x] Add a nuxt auth middleware
- [x] Refactor auth module
- [x] Add good linter config for nuxt
- [ ] Add a php linting (like ESLint, probably prettier with psr configuration) 
- [ ] Add a php cli user:create command
- [ ] Change reset password behaviour to less secure but more comfortable
