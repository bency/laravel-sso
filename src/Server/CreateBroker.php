<?php

namespace Casperlaitw\LaravelSSO\Server;

use Casperlaitw\LaravelSSO\Models\Broker;
use Illuminate\Support\Str;

/**
 * Class CreateBroker
 *
 * @package Casperlaitw\LaravelSSO\Server
 */
class CreateBroker
{
    /**
     * @param $name
     * @param $domain
     *
     * @return Broker
     */
    public function generate($name, $domain)
    {
        return Broker::create([
            'name' => Str::slug($name),
            'secret' => Str::random(40),
            'domains' => [$domain],
        ]);
    }
}
