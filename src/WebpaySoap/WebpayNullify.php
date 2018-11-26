<?php

namespace Transbank\Wrapper\WebpaySoap;

use Exception;
use Transbank\Wrapper\Helpers\Fluent;

/**
 * Class WebpayNullify
 *
 * This class allows the commerce to nullify a Transaction, totally or parcially.
 *
 * @package Transbank\Wrapper\WebpaySoap
 */
class WebpayNullify extends Transaction
{
    /**
     * Endpoint type to use
     *
     * @var string
     */
    protected $endpointType = 'commerce';

    /**
     * Nulls a Transaction in WebpaySoap
     *
     * @param string $authorizationCode
     * @param float $authorizedAmount
     * @param string $buyOrder
     * @param float $nullifyAmount
     * @param int $commerceCode
     * @return mixed
     * @throws \Transbank\Wrapper\Exceptions\Webpay\ErrorResponseException
     */
    public function nullify($authorizationCode, $authorizedAmount, $buyOrder, $nullifyAmount, $commerceCode)
    {

        try {
            $transaction = new Fluent([
                // Transaction Code or Capture Authorization Code
                'authorizationCode' => $authorizationCode,
                // Authorized Transaction amount to null (substract), or full Capture Amount
                'authorizedAmount' => $authorizedAmount,
                'buyOrder' => $buyOrder,
                'commerceId' => floatval($commerceCode ? $commerceCode : $this->credentials['commerceCode']),
                'nullifyAmount' => $nullifyAmount
            ]);


            $response = $this->performNullify($transaction);

            // If the validation is successful, return the results
            return $this->validate()
                ? $response
                : $this->throwException();

        } catch (Exception $e) {
            return $this->throwExceptionWithMessage($e->getMessage());
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
