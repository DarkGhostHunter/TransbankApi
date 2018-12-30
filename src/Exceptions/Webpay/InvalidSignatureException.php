<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class InvalidSignatureException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'Validation of Webpay WebpayClient returned false. Check credentials.';
}