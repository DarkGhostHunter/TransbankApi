<?php

namespace DarkGhostHunter\TransbankApi\Clients\Onepay;

use DarkGhostHunter\TransbankApi\Clients\AbstractConnector;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepayResponseErrorException;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepayValidationException;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;
use GuzzleHttp\Client;

class OnepayHttp extends AbstractConnector
{


    /**
     * Endpoints for environment types
     *
     * @var array
     */
    protected static $endpoints = [
        'integration'   => 'https://onepay.ionix.cl/ewallet-plugin-api-services/services/transactionservice/',
        'production'    => 'https://www.onepay.cl/ewallet-plugin-api-services/services/transactionservice/',
    ];

    /**
     * Endpoint to use
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Guzzle Client
     *
     * @var Client
     */
    protected $httpClient;

    /*
    |--------------------------------------------------------------------------
    | Booting
    |--------------------------------------------------------------------------
    */

    /**
     * Boot the connector
     *
     * @return mixed
     */
    public function boot()
    {
        $this->bootEndpoint();

        $this->bootHttpClient();
    }

    /**
     * Boots the default endpoint to use against the service
     *
     * @return void
     */
    protected function bootEndpoint()
    {
        $this->endpoint = self::$endpoints[$this->isProduction ? 'production' : 'integration'];
    }

    /**
     * Boots the HTTP Client to connect to the service
     *
     * @return void
     */
    protected function bootHttpClient()
    {
        $this->httpClient = new Client([
            'base_uri' => $this->endpoint,
            'timeout'  => 15.0,
            'verify' => true,
            'headers' => [
                'Content-type: application/json'
            ]
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Http Client
    |--------------------------------------------------------------------------
    */

    /**
     * Send a POST to the selected Onepay endpoint with the signed transaction,
     * and returns its content in an array;
     *
     * @param string $endpoint
     * @param OnepayTransaction $transaction
     * @return \stdClass
     * @throws OnepayResponseErrorException
     */
    protected function post(string $endpoint, OnepayTransaction $transaction)
    {
        $response = json_decode(
            $this->httpClient->post(
                $endpoint,
                ['body' => json_encode($transaction, JSON_UNESCAPED_SLASHES)]
            )->getBody()->getContents()
        );
        if ($response->responseCode === 'OK') {
            return $response->result;
        }

        throw new OnepayResponseErrorException($response->responseCode, $response->description, $transaction);
    }


    /*
    |--------------------------------------------------------------------------
    | Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Commits a Onepay Transaction on Transbank servers
     *
     * @param OnepayTransaction $transaction
     * @return array
     * @throws OnepayValidationException
     * @throws OnepayResponseErrorException
     */
    public function commit(OnepayTransaction $transaction)
    {
        $response = $this->post(
            'sendtransaction',
            $this->signedCommitTransaction($transaction)
        );

        if ($this->verifyCommitResponse($response, $transaction)) {
            return (array)$response;
        }

    }

    /**
     * Confirms a Onepay Transaction on Transbank servers
     *
     * @param OnepayTransaction $transaction
     * @return array
     * @throws OnepayValidationException
     * @throws OnepayResponseErrorException
     */
    public function confirm(OnepayTransaction $transaction)
    {
        $response = $this->post(
            'gettransactionnumber',
            $this->signedConfirmTransaction($transaction)
        );

        if ($this->verifyConfirmResponse($response, $transaction)) {
            return (array)$response;
        }
    }

    /**
     * Refunds a Onepay Transaction on Transbank servers
     *
     * @param OnepayTransaction $transaction
     * @return array
     * @throws OnepayResponseErrorException
     */
    public function refund(OnepayTransaction $transaction)
    {
        return (array)$this->post(
            'nullifytransaction',
            $this->signedRefundTransaction($transaction)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Verification
    |--------------------------------------------------------------------------
    */

    protected function verify($response, $data, $transaction)
    {
        $signature = base64_encode(
            hash_hmac('sha256', $data, $this->credentials->secret, true)
        );

        if ($signature === $response->signature) {
            return true;
        }

        throw new OnepayValidationException($transaction);
    }

    /**
     * Verifies a Onepay Transaction Commit Response
     *
     * @param $response
     * @param $transaction
     * @return bool
     * @throws OnepayValidationException
     */
    protected function verifyCommitResponse($response, $transaction)
    {
        $data = mb_strlen($response->occ) . $response->occ;
        $data .= mb_strlen($response->externalUniqueNumber) . $response->externalUniqueNumber;
        $data .= mb_strlen($response->issuedAt) . $response->issuedAt;

        return $this->verify($response, $data, $transaction);
    }

    /**
     * Verifies a Onepay Transaction Confirm Response
     *
     * @param $response
     * @param $transaction
     * @return bool
     * @throws OnepayValidationException
     */
    protected function verifyConfirmResponse($response, $transaction)
    {
        $data = mb_strlen($response->occ) . $response->occ
            . mb_strlen($response->authorizationCode) . $response->authorizationCode
            . mb_strlen($response->issuedAt) . $response->issuedAt
            . mb_strlen($response->amount) . $response->amount
            . mb_strlen($response->installmentsAmount) . $response->installmentsAmount
            . mb_strlen($response->installmentsNumber) . $response->installmentsNumber
            . mb_strlen($response->buyOrder) . $response->buyOrder;

        return $this->verify($response, $data, $transaction);
    }

    /*
    |--------------------------------------------------------------------------
    | Sign processes
    |--------------------------------------------------------------------------
    */

    /**
     * Returns a signed a OnepayTransaction using the Credential's Secret
     *
     * @param OnepayTransaction $transaction
     * @param string $data
     * @return OnepayTransaction
     */
    protected function sign(OnepayTransaction $transaction, string $data)
    {
        return (clone $transaction)
            ->appKey($this->credentials->appKey)
            ->apiKey($this->credentials->apiKey)
            ->signature(
                base64_encode(
                    hash_hmac('sha256', $data, $this->credentials->secret, true)
                )
            );
    }

    /**
     * Returns a signed Commit OnepayTransaction
     *
     * @param OnepayTransaction $transaction
     * @return OnepayTransaction
     */
    protected function signedCommitTransaction(OnepayTransaction $transaction)
    {
        $data = mb_strlen($transaction->externalUniqueNumber) . $transaction->externalUniqueNumber
            . mb_strlen($transaction->total) . $transaction->total
            . mb_strlen($transaction->itemsQuantity) . $transaction->itemsQuantity
            . mb_strlen($transaction->issuedAt) . $transaction->issuedAt
            . mb_strlen($transaction->callbackUrl) . $transaction->callbackUrl;

        return $this->sign($transaction, $data);
    }

    /**
     * Returns a signed Confirm OnepayTransaction
     *
     * @param OnepayTransaction $transaction
     * @return OnepayTransaction
     */
    protected function signedConfirmTransaction(OnepayTransaction $transaction)
    {
        $data = mb_strlen($transaction->occ) . $transaction->occ
            . mb_strlen($transaction->externalUniqueNumber) . $transaction->externalUniqueNumber
            . mb_strlen($transaction->issuedAt) . $transaction->issuedAt;

        return $this->sign($transaction, $data);
    }

    /**
     * Returns a signed Refund OnepayTransaction
     *
     * @param OnepayTransaction $transaction
     * @return OnepayTransaction
     */
    protected function signedRefundTransaction(OnepayTransaction $transaction)
    {
        $data = mb_strlen($transaction->externalUniqueNumber) . $transaction->externalUniqueNumber
            . mb_strlen($transaction->total) . $transaction->total
            . mb_strlen($transaction->itemsQuantity) . $transaction->itemsQuantity
            . mb_strlen($transaction->issuedAt) . $transaction->issuedAt
            . mb_strlen($transaction->callbackUrl) . $transaction->callbackUrl;

        return $this->sign($transaction, $data);
    }
}