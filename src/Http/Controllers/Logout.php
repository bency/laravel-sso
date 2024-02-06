<?php

namespace Casperlaitw\LaravelSSO\Http\Controllers;

use Casperlaitw\LaravelSSO\Server\Server;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Logout extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \Illuminate\Contracts\Auth\StatefulGuard $guard
     * @param \Casperlaitw\LaravelSSO\Server\Server    $server
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, StatefulGuard $guard, Server $server)
    {
        $server->logout();

        return response()->json([]);
    }
}
