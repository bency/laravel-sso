<?php

namespace Casperlaitw\LaravelSSO\Http\Middleware;

use Casperlaitw\LaravelSSO\Broker\Broker;
use Casperlaitw\LaravelSSO\LaravelSSO;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Class AutoAttach
 *
 * @package Casperlaitw\LaravelSSO\Http\Middleware
 */
abstract class AutoAttach
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Illuminate\Http\RedirectResponse|mixed|null
     */
    public function handle(Request $request, Closure $next)
    {
        $broker = new Broker();

        if ($error = $broker->hasErrors($request)) {
            if ($redirect = $this->sessionExpired($error, $request)) {
                return $redirect;
            }

            abort(400, $error);
        }

        if ($response = $broker->verifyWithResponse($request)) {
            return $response;
        }

        try {
            if ($response = $broker->attach()) {
                return $response;
            }

            if (! config('laravel-sso.ajax-login')) {
                if (auth()->guest()) {
                    if ($user = $broker->loginCurrentUser()) {
                        call_user_func(LaravelSSO::$authenticateUsingCallback, $user);
                    }
                }
            }

            return $next($request);
        } catch (RuntimeException $ex) {
            if ($redirect = $this->sessionExpired($ex->getMessage(), $request)) {
                return $redirect;
            }

            throw $ex;
        }
    }

    /**
     * Handle session expired or already taken by some other browsers
     *
     * @param                          $message
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function sessionExpired($message, Request $request): ?\Illuminate\Http\RedirectResponse
    {
        if (
            Str::contains($message, 'Session has expired') ||
            Str::contains($message, 'Token is already attached') ||
            Str::contains($message, 'Bearer token isn\'t attached to a client session')
        ) {
            // clear cookie and reattach
            Cookie::queue(Cookie::forget('sso_verify_'.config('laravel-sso.broker_name')));
            Cookie::queue(Cookie::forget('sso_token_'.config('laravel-sso.broker_name')));

            return $this->sessionExpiredResponse($request);
        }

        return null;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sessionExpiredResponse(Request $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->to($request->fullUrl());
    }

    /**
     * @param $message
     *
     * @return mixed
     */
    abstract public function handleException($message);
}
