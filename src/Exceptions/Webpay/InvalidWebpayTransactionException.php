<?php

namespace Transbank\Wrapper\Exceptions\Webpay;

use Throwable;
use Transbank\Wrapper\Contracts\TransactionInterface;
use Transbank\Wrapper\Exceptions\TransbankException;

class InvalidWebpayTransactionException extends \Exception implements TransbankException, WebpayException
{

    public function __construct(TransactionInterface $transaction, Throwable $previous = null)
    {
        $message = "This transaction is malformed or the credentials are not valid: \n $transaction";

        parent::__construct($message, 500, $previous);
    }


}