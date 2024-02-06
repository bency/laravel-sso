<?php

namespace Casperlaitw\LaravelSSO;

/**
 * Class LaravelSSO
 *
 * @package Casperlaitw\LaravelSSO
 */
class LaravelSSO
{
    /**
     * The callback that is responsible for validating authentication credentials, if applicable.
     *
     * @var callable|null
     */
    public static $authenticateUsingCallback;

    /**
     * The callback that is responsible for login and saving user to session, if applicable.
     *
     * @var callable|null
     */
    public static $serverAuthenticateUsingCallback;

    /**
     * @param callable $callback
     */
    public static function authenticateUsing(callable $callback)
    {
        static::$authenticateUsingCallback = $callback;
    }

    /**
     * @param callable $callback
     */
    public static function serverAuthenticateUsing(callable $callback)
    {
        static::$serverAuthenticateUsingCallback = $callback;
    }
}
