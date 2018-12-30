<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Onepay;

use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;
use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class OnepayResponseErrorException extends \Exception implements TransbankException, OnepayException
{

    /**
     * OnepayResponseErrorException constructor.
     * @param $error
     * @param $description
     * @param OnepayTransactionOnepayNullifyTransaction $transaction
     */
    public function __construct($error, $description, $transaction)
    {
        parent::__construct(
            "Onepay has returned an error: [$error] $description. Transaction: $transaction"
        );
    }
}