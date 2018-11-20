<?php

namespace Transbank\Wrapper\Exceptions\Onepay;

use Throwable;
use Transbank\Wrapper\Exceptions\TransbankException;
use Transbank\Wrapper\Transactions\OnepayTransaction;

class CartNegativeAmountException extends \Exception implements TransbankException, OnepayException
{

    protected $message = 'Cannot send to Onepay a Transaction with zero total amount or below.';

    public function __construct(OnepayTransaction $transaction)
    {
        $this->message .= "\n" . "Transaction: " . $transaction;

        parent::__construct();
    }

}