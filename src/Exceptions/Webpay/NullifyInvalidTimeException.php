<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class NullifyInvalidTimeException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'WebpayClient Invalidation is being called after the valid period.';
}