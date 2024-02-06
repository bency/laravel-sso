<?php

namespace Casperlaitw\LaravelSSO\Exceptions;

use RuntimeException;

class BrokerIsNotAttachedException extends RuntimeException
{
    protected $message = 'Broker is not attached';
}
