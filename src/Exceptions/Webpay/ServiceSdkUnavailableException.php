<?php

namespace Transbank\Wrapper\Exceptions\Webpay;

use Transbank\Wrapper\Exceptions\TransbankException;

class ServiceSdkUnavailableException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'This service is not yet enabled on the official Transbank SDK.';
}