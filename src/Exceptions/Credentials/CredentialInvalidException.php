<?php

namespace Transbank\Wrapper\Exceptions\Credentials;


use Transbank\Wrapper\Exceptions\TransbankException;

class CredentialInvalidException extends \Exception implements TransbankException
{
    protected $message = 'The Credential for this Transbank Service is invalid or malformed.';
}