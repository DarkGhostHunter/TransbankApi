<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Transbank;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class ValidationException extends \Exception implements TransbankException
{
    protected $message = "Transbank Response validation has returned false";
}