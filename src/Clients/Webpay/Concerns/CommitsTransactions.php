<?php


namespace DarkGhostHunter\TransbankApi\Clients\Webpay\Concerns;

/**
 * Trait CommitsTransactions
 *
 * @package DarkGhostHunter\TransbankApi\Clients\Webpay\Concerns
 *
 * @mixin \DarkGhostHunter\TransbankApi\Clients\Webpay\WebpayClient
 */
trait CommitsTransactions
{
    /**
     * Performs the commitment of the Transaction on Webpay
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
