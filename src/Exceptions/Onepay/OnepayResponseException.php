<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Onepay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;
use Throwable;

class OnepayResponseException extends \Exception implements TransbankException, OnepayException
{

    /**
     * OnepayResponseErrorException constructor.
     * @param $transaction
     * @param string $message
     * @param string $code
     * @param Throwable|null $previous
     */
    public function __construct($transaction, string $message, string $code = '0', Throwable $previous = null)
    {
        parent::__construct(
            "Onepay has returned an error: [$code] - $message \nTransaction: $transaction",
            0,
            $previous
        );
    }
}