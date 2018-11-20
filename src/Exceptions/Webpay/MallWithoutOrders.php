<?php

namespace Transbank\Wrapper\Exceptions\Webpay;

use Transbank\Wrapper\Exceptions\TransbankException;

class MallWithoutOrders extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'The Mall Transaction has no orders';
}