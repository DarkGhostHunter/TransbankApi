<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class ServiceSdkUnavailableException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'This service is not yet enabled on the official Transbank SDK.';
}