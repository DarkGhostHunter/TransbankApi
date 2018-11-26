<?php

namespace Transbank\Wrapper\Exceptions\Webpay;

use Throwable;
use Transbank\Wrapper\Exceptions\TransbankException;

class ErrorResponseException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'Webpay returned an error as response: ';

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->message .= $message;

        parent::__construct($message, $code, $previous);
    }
}