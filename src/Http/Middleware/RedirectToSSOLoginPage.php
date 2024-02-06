<?php

namespace Casperlaitw\LaravelSSO\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectToSSOLoginPage
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Illuminate\Http\RedirectResponse|mixed|null
     */
    public function handle(Request $request, Closure $next)
    {
        $url = $request->query('return_url');
        if (! $url) {
            $url = url()->previous();
        }

        return redirect()->to(config('laravel-sso.sso_server_url').'/login?return_url='.$url);
    }
}
