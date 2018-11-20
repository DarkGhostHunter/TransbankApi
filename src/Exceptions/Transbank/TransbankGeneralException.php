<?php

namespace Transbank\Wrapper\Exceptions\Transbank;

use Transbank\Wrapper\Exceptions\TransbankException;

class TransbankGeneralException extends \Exception implements TransbankException
{
    protected $message = "Unknown error from Transbank";
}