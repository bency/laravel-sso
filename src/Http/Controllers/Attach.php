<?php

namespace Casperlaitw\LaravelSSO\Http\Controllers;

use Casperlaitw\LaravelSSO\Server\Server;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Attach extends Controller
{
    /**
     * @param \Illuminate\Http\Request              $request
     * @param \Casperlaitw\LaravelSSO\Server\Server $server
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request, Server $server)
    {
        return $server->attachWithResponse($request);
    }
}
