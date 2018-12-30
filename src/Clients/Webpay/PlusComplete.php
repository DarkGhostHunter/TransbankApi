<?php

namespace DarkGhostHunter\TransbankApi\Clients\Webpay;

use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException;
use Exception;

class PlusComplete extends WebpayClient
{
    /**
     * Endpoint type to use
     *
     * @var string
     */
    protected $endpointType = 'complete';

    /**
     * Initializes a transaction WebpaySoap
     *
     * @param WebpayTransaction $transaction
     * @return mixed
     * @throws ErrorResponseException
     */
    public function complete(WebpayTransaction $transaction)
    {
        $transaction = (object)[
            'transactionType' => 'TR_COMPLETA_WS',
            'sessionId' => $transaction->sessionId,
            'cardDetail' => (object)[
                'cardExpirationDate' => $transaction->cardExpirationDate,
                'cvv' => $transaction->cvv,
                'cardNumber' => $transaction->cardNumber,
            ],
            'transactionDetails' => (object)[
                'amount' => $transaction->amount,
                'buyOrder' => $transaction->buyOrder,
                'commerceCode' => $this->credentials->commerceCode,
            ]
        ];


        try {
            // Perform the capture with the data, and return if validates
            if (($response = $this->performComplete($transaction)) && $this->validate())
                return $response;
        } catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage(), $e->getCode(), $e);
        }

    }

    /**
     * Returns the installments values
     *
     * @param $token
     * @param $buyOrder
     * @param $shareNumber
     * @return mixed
     * @throws ErrorResponseException
     */
    public function queryShare($token, $buyOrder, $shareNumber)
    {
        $queryShare = (object)[
            'token' => $token,
            'buyOrder' => $buyOrder,
            'shareNumber' => $shareNumber,
        ];

        try {
            if (($response = $this->performQueryShare($queryShare)) && $this->validate())
                return $response;
        } catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Authorizes the transaction, with or without installments.
     *
     * @param WebpayTransaction $transaction
     * @return mixed
     * @throws ErrorResponseException
     */
    public function charge(WebpayTransaction $transaction)
    {
        $charge = (object)[
            'token' => $transaction->token,
            'paymentTypeList' => (object)[
                'buyOrder' => $transaction->buyOrder,
                'commerceCode' => $this->credentials->commerceCode,
                'gracePeriod' => $transaction->gracePeriod,
                'queryShareInput' => (object)[
                    'idQueryShare' => $transaction->idQueryShare,
                ]
            ]
        ];

        // Get a installment by the given offset.
        if ($transaction->deferredPeriodIndex !== 0) {
            $charge->paymentTypeList->queryShareInput->deferredPeriodIndex = $transaction->deferredPeriodIndex;
        }

        try {
            // Perform the authorization
            if (($response = $this->performCharge($charge)) && $this->validate())
                return $response;

        } catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Acknowledges the WebpayClient result
     *
     * @param $token
     * @return bool
     * @throws ErrorResponseException
     */
    public function confirm($token)
    {
        $acknowledgeTransaction = (object)[
            'tokenInput' => $token
        ];

        try {
            if ($this->performConfirm($acknowledgeTransaction) && $validates = $this->validate())
                return $validates;
        } catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage(), $e->getCode(), $e);
        }

    }

    /**
     * Performs the acknowledge to WebpaySoap, which means to accept the transaction result
     *
     * @param $transaction
     * @return array
     */
    protected function performConfirm($transaction)
    {
        return (array)(
            $this->connector->acknowledgeCompleteTransaction($transaction)
        )->return;
    }

    /**
     * Authorizes the transaction, with or without installments ("cuotas").
     *
     * @param $transaction
     * @return array
     */
    protected function performCharge($transaction)
    {
        return (array)(
            $this->connector->authorize($transaction)
        )->return;
    }

    /**
     * Allows to retrieve each installment value
     *
     * @param $queryShare
     * @return array
     */
    protected function performQueryShare($queryShare)
    {
        return (array)(
            $this->connector->queryShare($queryShare)
        )->return;
    }

    /**
     * Initializes a transaction in WebpaySoap, returning the token transaction
     *
     * @param $transaction
     * @return array
     */
    protected function performComplete($transaction)
    {
        return (array)(
            $this->connector->initCompleteTransaction([
                'wsCompleteInitTransactionInput' => $transaction
            ])
        )->return;
    }

}
