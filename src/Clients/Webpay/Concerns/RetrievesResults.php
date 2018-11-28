<?php

namespace DarkGhostHunter\TransbankApi\Clients\Webpay\Concerns;

use DarkGhostHunter\TransbankApi\Helpers\Fluent;

/**
 * Trait RetrievesResults
 *
 * @package DarkGhostHunter\TransbankApi\Clients\Webpay\Concerns
 *
 * @mixin \DarkGhostHunter\TransbankApi\Clients\Webpay\Transaction
 */
trait RetrievesResults
{
    /**
     * Returns the Transaction results
     *
     * @param $transaction
     * @return array
     */
    public function retrieve($transaction)
    {
        return (array)($this->connector->getTransactionResult(
            new Fluent([
                'tokenInput' => $transaction
            ])
        ))->return;
    }
}
