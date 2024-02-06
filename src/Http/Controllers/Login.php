<?php

namespace Casperlaitw\LaravelSSO\Http\Controllers;

use Casperlaitw\LaravelSSO\Server\Server;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Login extends Controller
{
    public function __invoke(Request $request, Server $server)
    {
        try {
            if ($user = $server->login($request)) {
                return response()->json(['user' => $user]);
            }
            return response()->json(['error' => 'Not found.'], 404);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 400);
        }
    }
}
