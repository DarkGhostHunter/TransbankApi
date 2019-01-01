<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Onepay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;
use Throwable;

class OnepayClientException extends \Exception implements TransbankException, OnepayException
{

    /**
     * OnepayResponseErrorException constructor.
     * @param string $transaction
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $transaction, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            "Onepay has returned an error: Transaction: $transaction",
            $code,
            $previous
        );
    }
}