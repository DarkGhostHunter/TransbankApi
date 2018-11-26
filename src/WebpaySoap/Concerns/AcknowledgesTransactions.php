<?php


namespace Transbank\Wrapper\WebpaySoap\Concerns;

use Transbank\Wrapper\Helpers\Fluent;

/**
 * Trait AcknowledgesTransactions
 * @package Transbank\Wrapper\WebpaySoap\Concerns
 *
 * @mixin \Transbank\Wrapper\WebpaySoap\Transaction
 */
trait AcknowledgesTransactions
{
    /**
     * Notifies WebpaySoap that the Transaction has been accepted
     *
     * @param $transaction
     * @return object
     */
    protected function performConfirm($transaction)
    {
        return $this->connector->acknowledgeTransaction($transaction);
    }

    /**
     * Acknowledges and accepts the Transaction
     *
     * @param $token
     * @return array
     */
    public function confirm($token)
    {
        $acknowledgeTransaction = new Fluent([
            'tokenInput' => $token
        ]);

        $this->performConfirm($acknowledgeTransaction);

        // Since we don't need any result, return the validation as an array
        return [$this->validate()];
    }
}
