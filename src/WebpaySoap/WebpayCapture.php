<?php

namespace Transbank\Wrapper\WebpaySoap;

use Exception;
use Transbank\Wrapper\Helpers\Fluent;

/**
 * Class WebpayCapture
 *
 * This class allows the commerce to capture an transaction made through WebpaySoap
 *
 * @package Transbank\Wrapper\WebpaySoap
 */
class WebpayCapture extends Transaction
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
     * @param string $authorizationCode
     * @param string|float|int $captureAmount
     * @param string $buyOrder
     * @param null $commerceId
     * @return mixed
     * @throws \Transbank\Wrapper\Exceptions\Webpay\ErrorResponseException
     */
    public function capture($authorizationCode, $captureAmount, $buyOrder, $commerceId = null)
    {
        try {
            $captureInput = new Fluent([
                // Authorization code for the Transaction to capture
                'authorizationCode' => $authorizationCode,
                // Buy Order of the Transaction to capture
                'buyOrder' => $buyOrder,
                // Amount to capture
                'captureAmount' => $captureAmount,
                // Commerce Code or Mall Commerce Code who did the target Transaction
                'commerceId' => $commerceId ?? $this->credentials['commerceCode'],
            ]);

            // Perform the capture with the data
            $response = $this->performCapture($captureInput);

            // If the validation is successful, return the results
            return $this->validate()
                ? $response
                : $this->throwException();

        } catch (Exception $e) {
            return $this->throwExceptionWithMessage($e->getMessage());
        }
    }

    /**
     * Performs the WebpaySoap Capture operation
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
