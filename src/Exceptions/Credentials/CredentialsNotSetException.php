<?php

namespace Transbank\Wrapper\Exceptions\Credentials;

use Transbank\Wrapper\Exceptions\TransbankException;

class CredentialsNotSetException extends \Exception implements TransbankException
{

    protected $message = 'The following credentials for this Transbank Service are not set:';

}