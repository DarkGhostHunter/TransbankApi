<?php


namespace Transbank\Wrapper\Exceptions\Transbank;


use Transbank\Wrapper\Exceptions\TransbankException;

class ValidationException extends \Exception implements TransbankException
{
    protected $message = "Transbank Response validation has returned false";
}