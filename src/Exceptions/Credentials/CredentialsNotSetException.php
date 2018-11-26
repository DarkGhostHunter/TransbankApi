<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Credentials;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class CredentialsNotSetException extends \Exception implements TransbankException
{

    protected $message = 'The following credentials for this Transbank Service are not set: ';

}