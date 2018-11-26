<?php


namespace Transbank\Wrapper\WebpaySoap\Concerns;

/**
 * Trait InitializesTransactions
 * @package Transbank\Wrapper\WebpaySoap\Concerns
 *
 * @mixin \Transbank\Wrapper\WebpaySoap\Transaction
 */
trait CommitsTransactions
{
    /**
     * Performs the commitment of the Transaction on WebpaySoap
     *
     * @param object $transaction
     * @return mixed
     */
    protected function performCommit($transaction)
    {
        return (array)($this->connector->initTransaction([
            'wsInitTransactionInput' => $transaction
        ]))->return;
    }
}
