<?php

namespace Transbank\Wrapper\Exceptions\Credentials;

use Transbank\Wrapper\Exceptions\TransbankException;

class CredentialsNotReadableException extends \Exception implements TransbankException
{
    public function __construct(string $service)
    {
        $this->message = "Couldn't retrieve Integration Credentials for service $service.";
    }
}