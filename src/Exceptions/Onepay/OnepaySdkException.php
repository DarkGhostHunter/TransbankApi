<?php

namespace Transbank\Wrapper\Exceptions\Onepay;

use Throwable;
use Transbank\Wrapper\Exceptions\TransbankException;

class OnepaySdkException extends \Exception implements TransbankException, OnepayException
{
    protected $message = 'Transbank SDK has reported an exception';

    /**
     * TransbankSdkException constructor.
     * @param Throwable|null $previous
     */
    public function __construct(Throwable $previous = null)
    {
        parent::__construct($this->message, 500, $previous);
    }
}