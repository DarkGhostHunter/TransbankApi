<?php

namespace DarkGhostHunter\TransbankApi;

use Exception;
use DarkGhostHunter\TransbankApi\Adapters\WebpaySoapAdapter;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\RetrievingNoTransactionTypeException;
use DarkGhostHunter\TransbankApi\Responses\WebpayOneclickResponse;
use DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse;
use DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse;
use DarkGhostHunter\TransbankApi\Exceptions\Credentials\CredentialsNotReadableException;
use DarkGhostHunter\TransbankApi\Helpers\Helpers;
use DarkGhostHunter\TransbankApi\ResponseFactories\WebpayResponseFactory;
use DarkGhostHunter\TransbankApi\TransactionFactories\WebpayTransactionFactory;

/**
 * Class WebpaySoap
 * @package DarkGhostHunter\TransbankApi
 * 
 * @method Transactions\WebpayTransaction       makeNormal(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createNormal(array $attributes)
 * @method Transactions\WebpayMallTransaction   makeMallNormal(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createMallNormal(array $attributes)
 * @method Transactions\WebpayTransaction       makeDefer(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createDefer(array $attributes)
 * @method Transactions\WebpayMallTransaction   makeMallDefer(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createMallDefer(array $attributes)
 * @method Transactions\WebpayTransaction       makeCapture(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createCapture(array $attributes)
 * @method Transactions\WebpayTransaction       makeMallCapture(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createMallCapture(array $attributes)
 * @method Transactions\WebpayTransaction       makeNullify(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createNullify(array $attributes)
 * @method Transactions\WebpayTransaction       makeRegistration(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createRegistration(array $attributes)
 * @method Transactions\WebpayTransaction       makeUnregistration(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createUnregistration(array $attributes)
 * @method Transactions\WebpayTransaction       makeCharge(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createCharge(array $attributes)
 * @method Transactions\WebpayTransaction       makeReverseCharge(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createReverseCharge(array $attributes)
 * @method Transactions\WebpayMallTransaction   makeMallCharge(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createMallCharge(array $attributes)
 * @method Transactions\WebpayMallTransaction   makeMallReverseCharge(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createMallReverseCharge(array $attributes)
 * @method Transactions\WebpayMallTransaction   makeMallNullify(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createMallNullify(array $attributes)
 * @method Transactions\WebpayMallTransaction   makeMallReverseNullify(array $attributes = [])
 * @method Responses\WebpayPlusResponse             createMallReverseNullify(array $attributes)
 *
 * @method WebpayPlusResponse       getNormal(string $transaction)
 * @method WebpayPlusResponse       retrieveNormal(string $transaction)
 * @method bool                     confirmNormal(string $transaction)
 *
 * @method WebpayPlusMallResponse   getMallNormal(string $transaction)
 * @method WebpayPlusMallResponse   retrieveMallNormal(string $transaction)
 * @method bool                     confirmMallNormal(string $transaction)
 *
 * @method WebpayPlusResponse       getRegistration(string $transaction)
 *
 */
class Webpay extends AbstractService
{
    /**
     * Name of the default WebpaySoap Public Certificate
     *
     * @const string
     */
    protected const WEBPAY_CERT = 'webpay.cert';

    /*
    |--------------------------------------------------------------------------
    | Booting
    |--------------------------------------------------------------------------
    */

    /**
     * Boot any logic needed for the Service, like the Adapter and Factory;
     *
     * @return void
     */
    public function bootAdapter()
    {
        $this->adapter = new WebpaySoapAdapter;
        $this->adapter->setIsProduction($this->isProduction());
    }

    /**
     * Instantiates (and/or boots) the Transaction Factory for the Service
     *
     * @return void
     */
    public function bootTransactionFactory()
    {
        $this->transactionFactory = new WebpayTransactionFactory($this, $this->defaults);
    }

    /**
     * Instantiates (and/or boots) the Result Factory for the Service
     *
     * @return void
     */
    public function bootResponseFactory()
    {
        $this->resultFactory = new WebpayResponseFactory($this);
    }

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    */

    /**
     * Get the Service Credentials for the Environment
     *
     * @return mixed
     */
    protected function getProductionCredentials()
    {
        return array_merge([
            'webpayCert' => $this->getWebpayCertForEnvironment(),
        ], $this->transbankConfig->getCredentials('webpay')->toArray());
    }

    /**
     * Retrieve the Integration Credentials depending on the Transaction type
     *
     * @param string $type
     * @return array
     * @throws CredentialsNotReadableException
     */
    protected function getIntegrationCredentials(string $type = null)
    {
        // Get the directory path for the credentials for the transaction
        $environmentDir = $this->environmentCredentialsDirectory();

        $directory = $environmentDir . $this->integrationCredentialsForType($type);

        // List the folder contents from the transaction $type
        $contents = Helpers::dirContents($directory);

        // Return the credentials or fail miserably
        $credentials = [
            'commerceCode' => $commerceCode = strtok($contents[0], '.'),
            'privateKey' => file_get_contents($directory . "$commerceCode.key"),
            'publicCert' => file_get_contents($directory . "$commerceCode.cert"),
            'webpayCert' => $this->getWebpayCertForEnvironment(),
        ];

        if ($credentials['privateKey'] && $credentials['publicCert'] && $credentials['webpayCert']) {
            return $credentials;
        }

        throw new CredentialsNotReadableException(
            Helpers::classBasename(static::class)
        );
    }

    /**
     * Returns the WebpaySoap Public Certificate depending on the environment
     *
     * @return bool|string
     */
    protected function getWebpayCertForEnvironment()
    {
        return file_get_contents(
            $this->environmentCredentialsDirectory() . self::WEBPAY_CERT
        );
    }

    /**
     * Gets the directory of credentials for the transaction type
     *
     * @param string $type
     * @return string
     */
    protected function integrationCredentialsForType(string $type)
    {
        switch (true) {
            case strpos($type, 'oneclick') !== false:
                $directory = 'webpay-oneclick-normal';
                break;
            case strpos($type, 'defer') !== false:
            case strpos($type, 'capture') !== false:
            case strpos($type, 'nullify') !== false:
                $directory = 'webpay-plus-capture';
                break;
            case strpos($type, 'mall') !== false:
                $directory = 'webpay-plus-mall';
                break;
            default:
                $directory = 'webpay-plus-normal';
                break;
        }

        return $directory . '/';
    }

    /*
    |--------------------------------------------------------------------------
    | Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Gets and Acknowledges a Transaction in Transbank
     *
     * @param $transaction
     * @param string|null $options
     * @return Contracts\ResponseInterface|WebpayPlusMallResponse|WebpayPlusResponse
     * @throws RetrievingNoTransactionTypeException
     */
    public function get($transaction, $options = null)
    {
        if (!is_string($options) ?? null) {
            throw new RetrievingNoTransactionTypeException;
        }

        return parent::get($transaction, $options);
    }

    /**
     * Retrieves a Transaction
     *
     * @param $transaction
     * @param $type
     * @return WebpayPlusResponse|WebpayPlusMallResponse|WebpayOneclickResponse
     */
    public function retrieveTransaction($transaction, $type)
    {
        // Set the correct adapter credentials
        $this->setAdapterCredentials($type);

        return $this->parseResponse(
            $this->adapter->retrieve($transaction, $type),
            $type
        );
    }

    /**
     * Confirms a Transaction
     *
     * @param $transaction
     * @param $type
     * @return bool
     */
    public function confirmTransaction($transaction, $type)
    {
        // Set the correct adapter credentials
        $this->setAdapterCredentials($type);

        return ($this->parseResponse(
            $this->adapter->confirm($transaction, $type),
            $type
        ))->isSuccess();
    }

    /*
    |--------------------------------------------------------------------------
    | Parsers
    |--------------------------------------------------------------------------
    */

    /**
     * Transform the adapter raw answer of a transaction commitment to a
     * more friendly Webpay Response or WebpayOneclickResponse
     *
     * @param array $result
     * @param string $type
     * @return WebpayPlusResponse
     */
    protected function parseResponse(array $result, string $type)
    {
        // Create the Response depending on the transaction type
        switch (true) {
            case strpos($type, 'oneclick') !== false:
                $response = new WebpayOneclickResponse($result);
                break;
            case strpos($type, 'mall') !== false:
                $response = new WebpayPlusMallResponse($result);
                break;
            default:
                $response = new WebpayPlusResponse($result);
        }

        // Add the Type to the Response
        $response->setType($type);

        // Set the status of the Response
        $response->setStatus();

        return $response;
    }
}