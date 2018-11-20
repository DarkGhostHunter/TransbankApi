<?php

namespace Transbank\Wrapper\Exceptions\Webpay;

use Transbank\Wrapper\Contracts\TransactionInterface;
use Transbank\Wrapper\Exceptions\TransbankException;

class InvalidWebpayTransactionException extends \Exception implements TransbankException, WebpayException
{

    /** @var TransactionInterface */
    private $transaction;

    /**
     * InvalidWebpayTransactionException constructor.
     *
     * @param string $message
     * @param TransactionInterface $transaction
     */
    public function __construct(?string $message, TransactionInterface $transaction)
    {
        // Get the *real* error message
        $string = $this->cutString($message ?? 'Transaction was not performed on Transbank');

        $this->message = $string['error'];
        $this->transaction = $transaction;

        $this->message = "$this->message\n";
        $this->message .= "Transaction details:\n";
        $this->message .= 'Type ' . $this->transaction->getType() . "\n";
        $this->message .= $this->transaction;

        $this->code = $string['code'];
    }

    /**
     * Clean the error
     *
     * @param string $string
     * @return array
     */
    protected function cutString(string $string)
    {
        $start = strpos($string, '(');
        $end = strpos($string, ')');

        if ($string && $end) {
            return [
                'code' => substr($string, $start, $end) ?? '',
                'error' => trim(substr($string, 0, $start)) ?? ''
            ];
        }

        return [
            'code' => 500,
            'error' => $string
        ];

    }
}