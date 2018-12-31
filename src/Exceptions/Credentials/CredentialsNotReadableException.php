<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Credentials;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;
use Throwable;

class CredentialsNotReadableException extends \Exception implements TransbankException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $message = "Couldn't retrieve Integration Credentials for service $message.";
        parent::__construct($message, $code, $previous);
    }
}