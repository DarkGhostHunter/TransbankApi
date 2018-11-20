<?php

namespace Transbank\Wrapper\Exceptions\Webpay;

use Transbank\Wrapper\Exceptions\TransbankException;

class TransactionExpiredException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'The transaction %s acknowledgement was outside the valid time window';
}