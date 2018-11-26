<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class InvalidSignatureException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'Validation of Webpay Transaction returned false. Check credentials.';
}