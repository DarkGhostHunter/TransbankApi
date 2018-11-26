<?php


namespace DarkGhostHunter\TransbankApi\WebpaySoap\Concerns;

/**
 * Trait CommitsTransactions
 *
 * @package DarkGhostHunter\TransbankApi\WebpaySoap\Concerns
 *
 * @mixin \DarkGhostHunter\TransbankApi\WebpaySoap\Transaction
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
