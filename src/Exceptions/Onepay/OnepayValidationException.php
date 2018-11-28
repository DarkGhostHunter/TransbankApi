<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Onepay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;
use Throwable;

class OnepayValidationException extends \Exception implements TransbankException, OnepayException
{
    public function __construct(OnepayException $transaction)
    {
        parent::__construct("Onepay response has invalid signature. Transaction is as follows: \n$transaction");
    }
}