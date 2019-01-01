<?php

namespace DarkGhostHunter\TransbankApi\Clients\Onepay;

use DarkGhostHunter\TransbankApi\Clients\AbstractClient;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepayClientException;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepayResponseException;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepayValidationException;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\Transactions\OnepayNullifyTransaction;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;
use GuzzleHttp\Client;

class OnepayClient extends AbstractClient
{
    /**
     * Path for services
     *
     * @const string
     */
    protected const PATH = '/ewallet-plugin-api-services/services/transactionservice/';

    /**
     * Endpoints for environment types
     *
     * @var array
     */
    protected static $endpoints = [
        'integration' => 'https://onepay.ionix.cl',
        'production' => 'https://www.onepay.cl',
    ];

    /**
     * Guzzle Client
     *
     * @var Client
     */
    protected $httpClient;

    /**
     * If the client has been booted
     *
     * @var bool
     */
    protected $booted = false;

    /*
    |--------------------------------------------------------------------------
    | Getters and Setters
    |--------------------------------------------------------------------------
    */

    /**
     * Set the HTTP Client
     *
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Set the HTTP Client
     *
     * @param $client
     */
    public function setHttpClient($client)
    {
        $this->httpClient = $client;
    }

    /*
    |--------------------------------------------------------------------------
    | Booting
    |--------------------------------------------------------------------------
    */

    /**
     * Boot the connector
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->booted) {
            $this->bootEndpoint();
            $this->bootHttpClient();
            $this->booted = true;
        }
    }

    /**
     * Boots the default endpoint to use against the service
     *
     * @return void
     */
    protected function bootEndpoint()
    {
        $this->endpoint = self::$endpoints[$this->isProduction ? 'production' : 'integration'] . self::PATH;
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
            'timeout' => 15.0,
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
     * @param OnepayTransaction|OnepayNullifyTransaction $transaction
     * @return \stdClass
     * @throws OnepayClientException
     * @throws OnepayResponseException
     */
    protected function post(string $endpoint, $transaction)
    {
        $showSecrets = (clone $transaction)->showSecrets();

        try {
            $response = $this->httpClient->post(
                $endpoint,
                ['body' => json_encode($showSecrets, JSON_UNESCAPED_SLASHES)]
            );
        } catch (\Throwable $throwable) {
            throw new OnepayClientException($transaction, 0, $throwable);
        }

        $content = json_decode($response->getBody()->getContents());


        // Proceed only if the Status Code is OK and the "responseCode" is "OK".
        if ($response->getStatusCode() === 200 && $content->responseCode === 'OK') {
            return $content->result;
        }

        throw new OnepayResponseException($transaction, $content->description, $content->responseCode);

    }


    /*
    |--------------------------------------------------------------------------
    | Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Commits a Onepay WebpayClient on Transbank servers
     *
     * @param OnepayTransaction $transaction
     * @return array
     * @throws OnepayValidationException
     * @throws OnepayClientException
     * @throws OnepayResponseException
     */
    public function commit(OnepayTransaction $transaction)
    {
        $response = $this->post(
            'sendtransaction',
            $this->signedCommitTransaction($transaction)
        );

        $this->verifyCommitResponse($response, $transaction);

        return (array)$response;
    }

    /**
     * Confirms a Onepay WebpayClient on Transbank servers
     *
     * @param OnepayTransaction $transaction
     * @return array
     * @throws OnepayValidationException
     * @throws OnepayClientException
     * @throws OnepayResponseException
     */
    public function confirm(OnepayTransaction $transaction)
    {
        $response = $this->post(
            'gettransactionnumber',
            $this->signedConfirmTransaction($transaction)
        );

        $this->verifyConfirmResponse($response, $transaction);

        return (array)$response;
    }

    /**
     * Refunds a Onepay WebpayClient on Transbank servers
     *
     * @param OnepayNullifyTransaction $transaction
     * @return array
     * @throws OnepayClientException
     * @throws OnepayResponseException
     */
    public function refund(OnepayNullifyTransaction $transaction)
    {
        return (array)$this->post(
            'nullifytransaction',
            $this->signedRefundTransaction($transaction)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Signature helper
    |--------------------------------------------------------------------------
    */

    /**
     * Creates a signable string from the properties of the transaction
     *
     * @param $properties
     * @param $transaction
     * @return string
     */
    protected function createSignableString(array $properties, $transaction)
    {
        $data = '';

        foreach ($properties as $property) {
            $data .= mb_strlen($transaction->{$property}) . $transaction->{$property};
        }

        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | Verification
    |--------------------------------------------------------------------------
    */

    /**
     * Verifies if the Response signature matches the transaction data signature
     *
     * @param $response
     * @param $properties
     * @param $transaction
     * @return bool
     * @throws OnepayValidationException
     */
    protected function verify($response, $properties, $transaction)
    {
        $data = $this->createSignableString($properties, $response);

        $signature = base64_encode(
            hash_hmac('sha256', $data, $this->credentials->secret, true)
        );

        if ($signature === $response->signature) {
            return true;
        }

        throw new OnepayValidationException($transaction);
    }

    /**
     * Verifies a Onepay WebpayClient Commit Response
     *
     * @param $response
     * @param $transaction
     * @return bool
     * @throws OnepayValidationException
     */
    protected function verifyCommitResponse($response, $transaction)
    {
        $properties = [
            'occ',
            'externalUniqueNumber',
            'issuedAt',
        ];

        return $this->verify($response, $properties, $transaction);
    }

    /**
     * Verifies a Onepay WebpayClient Confirm Response
     *
     * @param $response
     * @param $transaction
     * @return bool
     * @throws OnepayValidationException
     */
    protected function verifyConfirmResponse($response, $transaction)
    {
        $properties = [
            'occ',
            'authorizationCode',
            'issuedAt',
            'amount',
            'installmentsAmount',
            'installmentsNumber',
            'buyOrder',
        ];

        return $this->verify($response, $properties, $transaction);
    }

    /*
    |--------------------------------------------------------------------------
    | Sign processes
    |--------------------------------------------------------------------------
    */

    /**
     * Returns a signed a OnepayTransaction using the Credential's Secret
     *
     * @param OnepayTransaction|OnepayNullifyTransaction $transaction
     * @param array $properties
     * @return OnepayTransaction|OnepayNullifyTransaction
     */
    protected function sign($transaction, array $properties)
    {
        $data = $this->createSignableString($properties, $transaction);

        return $transaction
            ->appKey($this->credentials->appKey)
            ->apiKey($this->credentials->apiKey)
            ->signature(
                base64_encode(hash_hmac('sha256', $data, $this->credentials->secret, true))
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
        $properties = [
            'externalUniqueNumber',
            'total',
            'itemsQuantity',
            'issuedAt',
            'callbackUrl',
        ];

        return $this->sign($transaction, $properties);
    }

    /**
     * Returns a signed Confirm OnepayTransaction
     *
     * @param OnepayTransaction $transaction
     * @return OnepayTransaction
     */
    protected function signedConfirmTransaction(OnepayTransaction $transaction)
    {
        $properties = [
            'occ',
            'externalUniqueNumber',
            'issuedAt',
        ];

        return $this->sign($transaction, $properties);
    }

    /**
     * Returns a signed Refund OnepayTransaction
     *
     * @param OnepayNullifyTransaction $transaction
     * @return OnepayTransaction
     */
    protected function signedRefundTransaction(OnepayNullifyTransaction $transaction)
    {
        $properties = [
            'occ',
            'externalUniqueNumber',
            'authorizationCode',
            'issuedAt',
            'nullifyAmount',
        ];

        return $this->sign($transaction, $properties);
    }
}