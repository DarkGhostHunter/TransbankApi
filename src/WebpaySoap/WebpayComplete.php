<?php
namespace Transbank\Wrapper\WebpaySoap;

use Exception;
use Transbank\Wrapper\Helpers\Fluent;

class WebpayComplete extends Transaction
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
     * @param $amount
     * @param $buyOrder
     * @param $sessionId
     * @param $cardExpirationDate
     * @param $cvv
     * @param $cardNumber
     * @return mixed
     */
    public function complete($amount, $buyOrder, $sessionId, $cardExpirationDate, $cvv, $cardNumber)
    {

        try {

            $transaction = new Fluent([
                // Type of transactions. For this, it has to always be 'TR_COMLETA_WS'.
                'transactionType' => 'TR_COMPLETA_WS',
                'sessionId' => $sessionId,
                // Object with Credit Card information
                'cardDetail' => new Fluent([
                    'cardExpirationDate' => $cardExpirationDate,
                    'cvv' => $cvv,
                    'cardNumber' => $cardNumber,
                ]),
                // Object with all unique transaction details
                'transactionDetails' => new Fluent([
                    'amount' => $amount,
                    'buyOrder' => $buyOrder,
                    'commerceCode' => $this->credentials['commerceCode'],
                ])
            ]);


            // Perform the transaction
            $response = $this->performComplete($transaction);

            // If the validation is successful, return the results
            return $this->validate()
                ? $response->return
                : $this->throwException();

        } catch (Exception $e) {
            return $this->throwExceptionWithMessage($e->getMessage());
        }

    }

    /**
     * Returns the installments values
     *
     * @param $token
     * @param $buyOrder
     * @param $shareNumber
     * @return mixed
     */
    public function queryShare($token, $buyOrder, $shareNumber)
    {
        try {

            $queryShare = new Fluent([
                'token' => $token,
                'buyOrder' => $buyOrder,
                'shareNumber' => $shareNumber,
            ]);

            // Perform the Query Share transaction
            $response = $this->performQueryShare($queryShare);


            // If the validation is successful, return the results
            return $this->validate()
                ? $response->return
                : $this->throwException();

        } catch (Exception $e) {
            return $this->throwExceptionWithMessage($e->getMessage());
        }
    }

    /**
     * Authorizes the transaction, with or without installments.
     *
     * @param $token
     * @param $buyOrder
     * @param $gracePeriod
     * @param $idQueryShare
     * @param int $deferredPeriodIndex
     * @return mixed
     */
    public function charge($token, $buyOrder, $gracePeriod, $idQueryShare, $deferredPeriodIndex)
    {
        try {

            $authorize = new Fluent([
                'token' => $token,
                'paymentTypeList' => new Fluent([
                    'buyOrder' => $buyOrder,
                    'commerceCode' => $this->credentials['commerceCode'],
                    'gracePeriod' => $gracePeriod,
                    'queryShareInput' => new Fluent([
                        'idQueryShare' => $idQueryShare,
                    ])
                ])
            ]);

            // Get a installment by the given offset.
            if ($deferredPeriodIndex !== 0) {
                $authorize->paymentTypeList->queryShareInput->deferredPeriodIndex = $deferredPeriodIndex;
            }

            // Perform the authorization
            $response = $this->performCharge($authorize);

            // If the validation is successful, return the results
            return $this->validate()
                ? $response->return
                : $this->throwException();

        } catch (Exception $e) {
            return $this->throwExceptionWithMessage($e->getMessage());
        }
    }

    /**
     * Acknowledges the Transaction result
     *
     * @param $token
     * @return bool
     */
    public function confirm($token)
    {
        $acknowledgeTransaction = new Fluent([
            'tokenInput' => $token
        ]);

        // Perform the Transaction
        $this->performConfirm($acknowledgeTransaction);

        // Since we don't need the results, we just simply return if the validation passes
        return $this->validate();

    }

    /**
     * Performs the acknowledge to WebpaySoap, which means to accept the transaction result
     *
     * @param $transaction
     * @return mixed
     */
    protected function performConfirm($transaction)
    {
        return $this->connector->acknowledgeCompleteTransaction($transaction);
    }

    /**
     * Authorizes the transaction, with or without installments ("cuotas").
     *
     * @param $transaction
     * @return mixed
     */
    protected function performCharge($transaction)
    {
        return $this->connector->authorize($transaction);
    }

    /**
     * Allows to retrieve each installment value
     *
     * @param $queryShare
     * @return mixed
     */
    protected function performQueryShare($queryShare)
    {
        return $this->connector->queryShare($queryShare);
    }

    /**
     * Initializes a transaction in WebpaySoap, returning the token transaction
     *
     * @param $transaction
     * @return mixed
     */
    protected function performComplete($transaction)
    {
        return $this->connector->initCompleteTransaction([
            'wsCompleteInitTransactionInput' => $transaction
        ]);
    }

}
