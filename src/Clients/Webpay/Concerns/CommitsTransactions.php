<?php


namespace DarkGhostHunter\TransbankApi\Clients\Webpay\Concerns;

/**
 * Trait CommitsTransactions
 *
 * @package DarkGhostHunter\TransbankApi\Clients\Webpay\Concerns
 *
 * @mixin \DarkGhostHunter\TransbankApi\Clients\Webpay\Transaction
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
