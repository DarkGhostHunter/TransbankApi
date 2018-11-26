<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class TransactionExpiredException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'The transaction %s acknowledgement was outside the valid time window';
}