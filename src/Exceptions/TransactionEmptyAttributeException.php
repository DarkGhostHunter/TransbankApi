<?php

namespace DarkGhostHunter\TransbankApi\Exceptions;

class TransactionEmptyAttributeException extends \Exception implements TransbankException
{

    public function __construct($type, $mustFill)
    {
        parent::__construct("WebpayClient $type cannot have $mustFill empty before committing.");
    }
}