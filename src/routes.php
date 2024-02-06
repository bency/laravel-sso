<?php

use Casperlaitw\LaravelSSO\Http\Controllers\Attach;
use Casperlaitw\LaravelSSO\Http\Controllers\Brokers\Create;
use Casperlaitw\LaravelSSO\Http\Controllers\Login;
use Casperlaitw\LaravelSSO\Http\Controllers\Logout;
use Casperlaitw\LaravelSSO\Http\Controllers\UserInfo;

Route::middleware('api')->prefix('api')->group(function () {
    Route::any('sso/attach', Attach::class);
    Route::post('sso/login', Login::class);
    Route::get('sso/user-info', UserInfo::class);
    Route::post('sso/logout', Logout::class);

    Route::post('sso/brokers/create', Create::class);
});
