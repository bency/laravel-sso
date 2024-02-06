<?php

namespace Casperlaitw\LaravelSSO\Http\Controllers;

use Casperlaitw\LaravelSSO\Broker\Broker;
use Casperlaitw\LaravelSSO\LaravelSSO;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class AjaxUserInfo
 *
 * @package Casperlaitw\LaravelSSO\Http\Controllers
 */
class AjaxUserInfo extends Controller
{
    /**
     * @param \Illuminate\Http\Request              $request
     * @param \Casperlaitw\LaravelSSO\Broker\Broker $broker
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, Broker $broker)
    {
        $user = null;
        if (auth()->guest() && $user = $broker->loginCurrentUser()) {
            call_user_func(LaravelSSO::$authenticateUsingCallback, $user);
            $user = auth()->user();
        }


        return responder()
            ->success($user)
            ->respond($user ? 201 : 204);
    }
}
