<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;
use Throwable;

class ServiceSdkUnavailableException extends \Exception implements TransbankException, WebpayException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $message = "The Service for $message is not yet enabled on this SDK.";

        parent::__construct($message, $code, $previous);
    }
}