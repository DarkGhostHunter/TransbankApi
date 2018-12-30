<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class IncompleteBodyException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'The WebpayClient need the following attributes to be sent: ';
}