<?php

namespace DarkGhostHunter\TransbankApi\WebpaySoap;

use DarkGhostHunter\TransbankApi\Helpers\Fluent;

/**
 * Class WebpayNormal
 *
 * @package DarkGhostHunter\TransbankApi\WebpaySoap
 */
class WebpayNormal extends Transaction
{
    use Concerns\AcknowledgesTransactions,
        Concerns\RetrievesResults,
        Concerns\CommitsTransactions;

    /**
     * Endpoint type to use
     *
     * @var string
     */
    protected $endpointType = 'webpay';

    /**
     * Commits a Normal or Mall Transaction into WebpaySoap
     *
     * @param string $buyOrder
     * @param string|null $sessionId
     * @param string $urlReturn
     * @param string $urlFinal
     * @param $amountOrStores
     * @return array
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function commit(string $buyOrder,
                           ?string $sessionId,
                           string $urlReturn,
                           string $urlFinal,
                           $amountOrStores)
    {
        // Create a normal transaction using the Fluent Helper
        $transaction = new Fluent([
            'wSTransactionType' => 'TR_NORMAL_WS',
            'sessionId' => $sessionId,
            'buyOrder' => $buyOrder,
            'returnURL' => $urlReturn,
            'finalURL' => $urlFinal,
        ]);

        // If it's not a Mall transaction, then array it as one,
        // otherwise transform the transaction to comply with
        // a Mall Transaction.
        if (is_numeric($amountOrStores)) {
            $amountOrStores = [
                [
                    'commerceCode' => $this->credentials['commerceCode'],
                    'buyOrder' => $buyOrder,
                    'amount' => $amountOrStores,
                ]
            ];
        } else {
            $transaction->wSTransactionType = 'TR_MALL_WS';
            $transaction->commerceId = $this->credentials['commerceCode'];
        }

        // For each item (even if its one), transform it as something
        // Soap Connector can digest.
        foreach ($amountOrStores as $item) {
            $transactionDetails[] = new Fluent([
                'commerceCode' => $item['storeCode'] ?? $item['commerceCode'],
                'buyOrder' => $item['buyOrder'],
                'amount' => $item['amount'],
            ]);
        }

        $transaction->transactionDetails = $transactionDetails;

        // Now that we have the transaction completed, commit it
        $response = $this->performCommit($transaction);

        // If the validation is successful, return the results
        if ($this->validate())
            return $response;

        $this->throwException();

    }

    /**
     * Obtains the Transaction results from Webpay Soap
     *
     * @param string $token
     * @return array
     */
    public function get($token)
    {
        // Perform the Transaction result
        $response = $this->retrieve($token);

        // If Validation passes and the Transaction is Confirmed...
        if ($this->validate() && $this->confirm($token)) {
            // Extract the results from the response and return it
            return $response;
        }
    }

}
