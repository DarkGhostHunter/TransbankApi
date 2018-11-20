<?php

namespace Transbank\Wrapper\Exceptions\Transbank;

use Transbank\Wrapper\Exceptions\TransbankException;

class InvalidServiceException extends \Exception implements TransbankException
{
    protected $message = "The %s service does not exist in Transbank Wrapper (or is not enabled by them).";

    public function __construct(string $service)
    {
        $this->message = sprintf($this->message, $service);
    }
}