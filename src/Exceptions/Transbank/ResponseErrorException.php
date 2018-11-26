<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Transbank;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class ResponseErrorException extends \Exception implements TransbankException
{
    protected $message = 'Transbank responded with an HTTP error code: ';
}