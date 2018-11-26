<?php

namespace Transbank\Wrapper\Exceptions\Webpay;

use Transbank\Wrapper\Exceptions\TransbankException;

class InvalidSignatureException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'Validation of Webpay Transaction returned false. Check credentials.';
}