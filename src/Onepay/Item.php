<?php

namespace Transbank\Wrapper\Onepay;

use Transbank\Wrapper\Helpers\Fluent;

class Item extends Fluent
{
    /**
     * Attributes container.
     *
     * @var array
     */
    protected $attributes = [
        'quantity' => 1,
        'amount' => 0,
        'expire' => 0,
        'additionalData' => null
    ];
}