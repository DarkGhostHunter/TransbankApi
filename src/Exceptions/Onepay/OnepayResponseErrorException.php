<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Onepay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;
use Throwable;

class OnepayResponseErrorException extends \Exception implements TransbankException, OnepayException
{

    /**
     * OnepayResponseErrorException constructor.
     * @param $error
     * @param $description
     * @param \DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction|\DarkGhostHunter\TransbankApi\Transactions\OnepayNullifyTransaction $transaction
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($error, $description, $transaction, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            "Onepay has returned an error: [$error] $description. Transaction: $transaction",
            $code,
            $previous
        );
    }
}