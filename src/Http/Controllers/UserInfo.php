<?php

namespace Casperlaitw\LaravelSSO\Http\Controllers;

use Casperlaitw\LaravelSSO\Server\Server;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use RuntimeException;

class UserInfo extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request              $request
     * @param \Casperlaitw\LaravelSSO\Server\Server $server
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function __invoke(Request $request, Server $server)
    {
        try {
            if ($user = $server->getUserInfo()) {
                return response()->json(['user' => $user]);
            }

            return response()->json([], 204);
        } catch (RuntimeException $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        }
    }
}
