<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class MallWithoutOrders extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'The Mall WebpayClient has no orders';
}