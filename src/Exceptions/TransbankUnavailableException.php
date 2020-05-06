<?php

namespace DarkGhostHunter\TransbankApi\Exceptions;

use Exception;

class TransbankUnavailableException extends Exception implements TransbankException
{
    /**
     * Exception message
     *
     * @var string
     */
    protected $message = 'Transbank Services are unavailable';
}