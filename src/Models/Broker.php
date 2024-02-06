<?php

namespace Casperlaitw\LaravelSSO\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Broker
 *
 * @package Casperlaitw\LaravelSSO\Models
 */
class Broker extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'secret',
        'domains',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'domains' => 'array',
    ];
}
