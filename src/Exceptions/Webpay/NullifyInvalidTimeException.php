<?php

namespace Transbank\Wrapper\Exceptions\Webpay;

use Transbank\Wrapper\Exceptions\TransbankException;

class NullifyInvalidTimeException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'Transaction Invalidation is being called after the valid period.';
}