<?php

namespace DarkGhostHunter\TransbankApi\Clients\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException;
use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use Exception;

/**
 * Class PlusNullify
 *
 * This class allows the commerce to nullify a WebpayClient, totally or parcially.
 *
 * @package DarkGhostHunter\TransbankApi\WebpaySoap
 */
class PlusNullify extends WebpayClient
{
    /**
     * Endpoint type to use
     *
     * @var string
     */
    protected $endpointType = 'commerce';

    /**
     * Nulls a WebpayClient in WebpaySoap
     *
     * @param WebpayTransaction $transaction
     * @return mixed
     * @throws ErrorResponseException
     */
    public function nullify(WebpayTransaction $transaction)
    {
        $transaction = (object)[
            // WebpayClient Code or Capture Authorization Code
            'authorizationCode' => $transaction->authorizationCode,
            // Authorized WebpayClient amount to null (substract), or full Capture Amount
            'authorizedAmount' => $transaction->authorizedAmount,
            'buyOrder' => $transaction->buyOrder,
            'commerceId' => $transaction->commerceCode ?? $this->credentials->commerceCode,
            'nullifyAmount' => $transaction->nullifyAmount
        ];

        try {
            // Perform the capture with the data, and return if validates
            if (($response = $this->performNullify($transaction)) && $this->validate())
                return $response;
        } catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage(), $e->getCode(), $e);
        }

    }

    /**
     * Performs the Nullify on WebpaySoap
     *
     * @param $transaction
     * @return mixed
     */
    protected function performNullify($transaction)
    {
        return (array)($this->connector->nullify([
            'nullificationInput' => $transaction
        ]))->return;
    }

}
