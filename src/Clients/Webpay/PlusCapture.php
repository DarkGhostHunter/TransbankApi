<?php

namespace DarkGhostHunter\TransbankApi\Clients\Webpay;

use DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\InvalidSignatureException;
use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use Exception;

class PlusCapture extends WebpayClient
{
    /**
     * Endpoint type to use
     *
     * @var string
     */
    protected $endpointType = 'commerce';

    /**
     * Captures the transaction
     *
     * @param WebpayTransaction $transaction
     * @return mixed
     * @throws ErrorResponseException
     * @throws InvalidSignatureException
     */
    public function capture(WebpayTransaction $transaction)
    {
        $capture = (object)[
            'authorizationCode' => $transaction->authorizationCode,
            'buyOrder' => $transaction->buyOrder,
            'captureAmount' => $transaction->captureAmount,
            'commerceId' => $transaction->commerceId ?? $this->credentials->commerceCode,
        ];

        try {
            // Perform the capture with the data, and return if validates
            $response = $this->performCapture($capture);
        } catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage(), $e->getCode(), $e);
        }

        if ($this->validate()) {
            return $response;
        };

        throw new InvalidSignatureException();
    }

    /**
     * Performs the Webpay Soap Capture operation
     *
     * @param $capture
     * @return array
     */
    protected function performCapture($capture)
    {
        return (array)($this->connector->capture([
            'captureInput' => $capture
        ]))->return;
    }

}
