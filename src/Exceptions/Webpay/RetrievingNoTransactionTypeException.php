<?php

namespace Transbank\Wrapper\Exceptions\Webpay;

use Transbank\Wrapper\Exceptions\TransbankException;

class RetrievingNoTransactionTypeException extends \Exception implements TransbankException, WebpayException
{
    protected $message = 'Transaction retrieval must have a type so it can hit the correct Transbank endpoint.';
}