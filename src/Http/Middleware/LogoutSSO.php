<?php

namespace Casperlaitw\LaravelSSO\Http\Middleware;

use Casperlaitw\LaravelSSO\Broker\Broker;
use Closure;
use Illuminate\Http\Request;

class LogoutSSO
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
        $broker->logout();
        auth()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $next($request);
    }
}
