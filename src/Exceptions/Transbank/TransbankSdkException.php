<?php

namespace DarkGhostHunter\TransbankApi\Exceptions\Transbank;

use Throwable;
use DarkGhostHunter\TransbankApi\Exceptions\TransbankException;

class TransbankSdkException extends \Exception implements TransbankException
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