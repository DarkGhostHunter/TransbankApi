<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Transbank;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class TransbankGeneralException extends \Exception implements TransbankException
{
    protected $message = "Unknown error from Transbank";
}