<?php

namespace DarkGhostHunter\TransbankApi\Clients\Webpay\Concerns;

use DarkGhostHunter\Fluid\Fluid;

/**
 * Trait RetrievesResults
 *
 * @package DarkGhostHunter\TransbankApi\Clients\Webpay\Concerns
 *
 * @mixin \DarkGhostHunter\TransbankApi\Clients\Webpay\WebpayClient
 */
trait RetrievesResults
{
    /**
     * Returns the Transaction results
     *
     * @param string $transaction
     * @return array
     */
    public function retrieve(string $transaction)
    {
        return (array)($this->connector->getTransactionResult(
            new Fluid([
                'tokenInput' => $transaction
            ])
        ))->return;
    }
}
