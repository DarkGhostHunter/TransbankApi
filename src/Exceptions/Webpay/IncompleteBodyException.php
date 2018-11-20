<?php

namespace Transbank\Wrapper\Exceptions\Webpay;

use Transbank\Wrapper\Exceptions\TransbankException;

class IncompleteBodyException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'The Transaction need the following attributes to be sent: ';
}