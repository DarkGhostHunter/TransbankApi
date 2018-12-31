<?php


namespace DarkGhostHunter\TransbankApi\Clients\Webpay\Concerns;

use DarkGhostHunter\TransbankApi\Helpers\Fluent;

/**
 * Trait AcknowledgesTransactions
 * @package DarkGhostHunter\TransbankApi\Clients\Webpay\Concerns
 *
 * @mixin \DarkGhostHunter\TransbankApi\Clients\Webpay\WebpayClient
 */
trait AcknowledgesTransactions
{
    /**
     * Notifies WebpaySoap that the WebpayClient has been accepted
     *
     * @param $transaction
     * @return object
     */
    protected function performConfirm($transaction)
    {
        return $this->connector->acknowledgeTransaction($transaction);
    }

    /**
     * Acknowledges and accepts the WebpayClient
     *
     * @param $token
     * @return bool
     */
    public function confirm($token)
    {
        $acknowledgeTransaction = new Fluent([
            'tokenInput' => $token
        ]);

        $this->performConfirm($acknowledgeTransaction);

        // Since we don't need any result, return the validation as a boolean
        return $this->validate();
    }
}
