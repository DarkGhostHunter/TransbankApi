<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class IncompleteBodyException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'The Transaction need the following attributes to be sent: ';
}