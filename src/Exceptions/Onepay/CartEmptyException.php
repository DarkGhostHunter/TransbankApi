<?php

namespace Transbank\Wrapper\Exceptions\Onepay;

use Exception;
use Transbank\Wrapper\Exceptions\TransbankException;
use Transbank\Wrapper\Transactions\OnepayTransaction;

class CartEmptyException extends Exception implements TransbankException, OnepayException
{

    protected $message = 'Cannot send to Onepay an empty OnepayTransaction.';

    public function __construct(OnepayTransaction $transaction)
    {
        $this->message .= "\n" . "Transaction: " . $transaction;

        parent::__construct();
    }


}