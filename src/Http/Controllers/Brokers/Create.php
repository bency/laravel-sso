<?php

namespace Casperlaitw\LaravelSSO\Http\Controllers\Brokers;

use Casperlaitw\LaravelSSO\Server\CreateBroker;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class Create
 *
 * @package Casperlaitw\LaravelSSO\Http\Controllers\Brokers
 */
class Create extends Controller
{
    /**
     * @param \Illuminate\Http\Request                    $request
     * @param \Casperlaitw\LaravelSSO\Server\CreateBroker $broker
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, CreateBroker $broker)
    {
        $request->validate([
            'name' => 'required',
            'domain' => 'required',
        ]);

        return responder()
            ->success($broker->generate($request->input('name'), $request->input('domain')))
            ->respond(201);
    }
}
