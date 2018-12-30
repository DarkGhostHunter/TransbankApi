<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Credentials;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class CredentialInvalidException extends \Exception implements TransbankException
{

    public function __construct($service, $credential)
    {
        $type = getType($credential);

        $service = ucfirst($service);

        parent::__construct("The Credential for $service has to be a string, $type passed.");
    }
}