<?php

use Casperlaitw\LaravelSSO\Http\Controllers\AjaxUserInfo;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;

Route::middleware('api')->prefix('api')->group(function () {
    Route::get('sso/ajax/user-info', AjaxUserInfo::class)
        ->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
        ]);
});
