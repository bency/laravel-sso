# Laravel SSO

The Laravel Single Sign On Solution

## Installation

### Server

Install the package using composer.
```
composer require
```

#### Configuration and Migrations

Publish config and migrations
```
php artisan vendor:publish --provider="Casperlaitw\LaravelSSO\LaravelSSOProvider"
```

```
php artisan vendor:publish --provider="Casperlaitw\LaravelSSO\LaravelSSOProvider --tag=migrations"
```

#### Middleware
Update api middleware group.

```php
'api' => [
   \Illuminate\Session\Middleware\StartSession::class,
   \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```


### Client

Install the package using composer.
```
composer require
```

Publish config and migrations
```
php artisan vendor:publish --provider="Casperlaitw\LaravelSSO\LaravelSSOProvider"
```

Publish ajax login js
```
php artisan vendor:publish --force --tag=laravel-sso:assets
```

Add config to .env
```
SSO_TYPE=broker
SSO_SERVER_URL=https://single-sign-on-app.test
SSO_BROKER_NAME=
SSO_BROKER_SECRET=
```

#### Middleware

Create new Middleware and extends `AutoAttach` Middleware
```php
<?php

namespace App\Http\Middleware;

use App\Models\User;
use Casperlaitw\LaravelSSO\Http\Middleware\AutoAttach;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class SSOAttach
 *
 * @package App\Http\Middleware
 */
class SSOAttach extends AutoAttach
{
    /**
     * @param $message
     */
    public function handleException($message)
    {
        abort(400, $message);
    }
}
```

Add your authenticate logical in `AppServiceProvider.php`
```php
    use Casperlaitw\LaravelSSO\LaravelSSO;
    use App\Models\User;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        LaravelSSO::authenticateUsing(function (array $user) {
             // create your user and login
            $model = User::updateOrCreate([
                'email' => $user['email']
            ], Arr::only($user, ['name', 'email', 'password']));
    
            auth()->login($model);
        });
    }
```

then add the middleware to `web` middleware group and change the priority
```php
<?php
    /**
     * The priority-sorted list of middleware.
     *
     * Forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\SSOAttach::class,
        \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SSOAttach::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
```

#### Controller

In login, you will call SSO api for login user.
```php
<?php
    public function login(Request $request)
    {
        $broker = new Casperlaitw\LaravelSSO\Broker\Broker();
        $broker->login($request->input('email'), $request->input('password'));
    }
```

in Logout, you can just added a route url with `LogoutSSO` middleware

```php
<?php
 Route::get('logout', function () {
        return redirect('/');
    })
        ->middleware(\Casperlaitw\LaravelSSO\Http\Middleware\LogoutSSO::class);
```
