<?php

namespace Transbank\Wrapper\Exceptions\Transbank;

use Transbank\Wrapper\Exceptions\TransbankException;

class ResponseErrorException extends \Exception implements TransbankException
{
    protected $message = 'Transbank responded with an HTTP error code: ';
}