<?php

namespace Transbank\Wrapper\Exceptions\Transbank;

use Transbank\Wrapper\Exceptions\TransbankException;

class ServicesUnavailableException extends \Exception implements TransbankException
{
    protected $message = 'Cannot connect to Transbank Services. Code generated: ';
}