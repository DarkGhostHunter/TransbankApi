<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class NullifyInvalidAmount extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'Amount to nullify cannot be partial';
}