<?php

namespace DarkGhostHunter\TransbankApi\WebpaySoap\Concerns;

use DarkGhostHunter\TransbankApi\Helpers\Fluent;

/**
 * Trait RetrievesResults
 *
 * @package DarkGhostHunter\TransbankApi\WebpaySoap\Concerns
 *
 * @mixin \DarkGhostHunter\TransbankApi\WebpaySoap\Transaction
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
