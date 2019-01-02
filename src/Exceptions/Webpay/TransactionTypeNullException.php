<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class TransactionTypeNullException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'Transaction retrieval must have a type so it can hit the correct Transbank endpoint.';
}