<?php

namespace DarkGhostHunter\TransbankApi\Clients\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException;
use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use Exception;

class PlusNormal extends WebpayClient
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
     * Commits a Normal or Mall WebpayClient into WebpaySoap
     *
     * @param WebpayTransaction $transaction
     * @return array
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function commit(WebpayTransaction $transaction)
    {

        // Create a normal transaction using the Fluent Helper
        $commit = (object)[
            'wSTransactionType'     => 'TR_NORMAL_WS',
            'sessionId'             => $transaction->sessionId,
            'buyOrder'              => $transaction->buyOrder,
            'returnURL'             => $transaction->returnUrl,
            'finalURL'              => $transaction->finalUrl,
            'transactionDetails'    => $transaction->items,
        ];

        // If this is a normal transaction, which doesn't have items,
        // we will comply with Transaction structure and just add a
        // single item into the data to send.
        if (!$commit->transactionDetails) {
            $commit->transactionDetails = [
                [
                    'commerceCode' => $this->credentials->commerceCode,
                    'buyOrder' => $transaction->buyOrder,
                    'amount' => $transaction->amount,
                ]
            ];
        // Otherwise, we will change the transaction type to Mall and...
        } else {
            $commit->wSTransactionType = 'TR_MALL_WS';
            $commit->commerceId = $this->credentials->commerceCode;
        }

        // ..for each item (even if its one), transform it as something
        // Soap Connector can digest.
        foreach ($commit->transactionDetails as &$item) {
            $item = (object)[
                'commerceCode' => $item['commerceCode'],
                'buyOrder' => $item['buyOrder'],
                'amount' => $item['amount'],
            ];
        }

        try {
            // Now that we have the transaction completed, commit it
            if (($response = $this->performCommit($commit)) && $this->validate())
                return $response;
        } catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Obtains the WebpayClient results from Webpay Soap
     *
     * @param WebpayTransaction $transaction
     * @return array
     */
    public function retrieveAndConfirm(WebpayTransaction $transaction)
    {
        // Perform the WebpayClient result
        $response = $this->retrieve($transaction);

        // If Validation passes and the WebpayClient is Confirmed...
        if ($this->validate() && $this->confirm($transaction))
            // Extract the results from the response and return it
            return $response;
    }

}
