<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class RetrievingNoTransactionTypeException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'WebpayClient retrieval must have a type so it can hit the correct Transbank endpoint.';
}