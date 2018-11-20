<?php

namespace Transbank\Wrapper\Exceptions\Webpay;

use Transbank\Wrapper\Exceptions\TransbankException;

class NullifyInvalidAmount extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'Amount to nullify cannot be partial';
}