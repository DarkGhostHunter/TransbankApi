<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use Throwable;
use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class ErrorResponseException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'Webpay returned an error as response: ';

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $array = ['<!--' => '', '-->' => ''];

        $this->message .= str_replace(array_keys($array), array_values($array), $message);

        parent::__construct($message, 0, $previous);
    }
}