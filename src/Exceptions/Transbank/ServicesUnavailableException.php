<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Transbank;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class ServicesUnavailableException extends \Exception implements TransbankException
{
    protected $message = 'Cannot connect to Transbank Services. Code generated: ';
}