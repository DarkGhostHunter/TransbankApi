<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Onepay;

use Exception;
use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;

class CartEmptyException extends Exception implements TransbankException, OnepayException
{

    protected $message = 'Cannot send to Onepay an empty OnepayTransaction.';

    public function __construct(OnepayTransaction $transaction)
    {
        $this->message .= "\n" . "WebpayClient: " . $transaction;

        parent::__construct();
    }


}