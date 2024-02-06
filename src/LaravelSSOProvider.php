<?php

namespace Casperlaitw\LaravelSSO;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelSSOProvider
 *
 * @package Casperlaitw\LaravelSSO
 */
class LaravelSSOProvider extends ServiceProvider
{
    /**
     *
     */
    public function boot()
    {
        $this->registerPublishables();
        Blade::directive('laravelSSOAjaxScript', function () {
            if (config('laravel-sso.ajax-login')) {
                $url = mix('/app.js', 'vendor/laravel-sso');

                return "<script src=\"$url\"></script>";
            }
        });
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-sso.php', 'laravel-sso');

        if (config('laravel-sso.type') === 'server') {
            $this->loadRoutesFrom(realpath(__DIR__.'/routes.php'));
        }
        if (config('laravel-sso.ajax-login')) {
            $this->loadRoutesFrom(__DIR__.'/ajax.php');
        }
    }

    /**
     *
     */
    public function registerPublishables()
    {
        $this->publishes([
            __DIR__.'/../config/laravel-sso.php' => config_path('laravel-sso.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/2020_10_15_000000_create_brokers_table.php' => database_path('migrations/2020_10_15_000000_create_brokers_table.php'),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/laravel-sso'),
        ], 'laravel-sso:assets');
    }
}
