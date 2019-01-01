<?php

namespace DarkGhostHunter\TransbankApi;

use DarkGhostHunter\TransbankApi\Adapters\OnepayAdapter;
use DarkGhostHunter\TransbankApi\Responses\OnepayResponse;
use DarkGhostHunter\TransbankApi\TransactionFactories\OnepayTransactionFactory;

/**
 * Class Onepay
 * @package DarkGhostHunter\TransbankApi
 *
 * @method Transactions\OnepayTransaction           makeCart(array $attributes = [])
 * @method Responses\OnepayResponse                 createCart(array $attributes = [])
 * @method Transactions\OnepayNullifyTransaction    makeNullify(array $attributes = [])
 * @method Responses\OnepayResponse                 createNullify(array $attributes = [])
 *
 */
class Onepay extends AbstractService
{
    /**
     * Location of the Integration Keys
     *
     * @const string
     */
    protected const INTEGRATION_KEYS = 'integration/onepay-keys.php';

    /**
     * Location of the Production Keys
     *
     * @const string
     */
    protected const PRODUCTION_KEYS = 'production/onepay-keys.php';

    /**
     * WebpayClient Factory to use for forwarding calls
     *
     * @example \DarkGhostHunter\TransbankApi\Clients\Webpay\AbstractTransactionFactory::class
     * @var string
     */
    protected $transactionFactory;

    /*
    |--------------------------------------------------------------------------
    | Booting
    |--------------------------------------------------------------------------
    */

    /**
     * Boot any logic needed for the Service, like the Adapter and Factories;
     *
     * @return void
     */
    public function boot()
    {
        $this->bootAdapter();
        $this->bootTransactionFactory();
    }

    /**
     * Instantiates (and/or boots) the Adapter for the Service
     *
     * @return void
     */
    public function bootAdapter()
    {
        $this->adapter = new OnepayAdapter();
    }

    /**
     * Instantiates (and/or boots) the WebpayClient Factory for the Service
     *
     * @return void
     */
    public function bootTransactionFactory()
    {
        $this->transactionFactory = new OnepayTransactionFactory($this, $this->defaults);
    }

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    */

    /**
     * Get the Service Credentials for the Production Environment
     *
     * @return array
     */
    protected function getProductionCredentials()
    {
        return include __DIR__ . '/' .
            trim(self::CREDENTIALS_DIR, '/') . '/' .
            trim(self::PRODUCTION_KEYS, '/');
    }

    /**
     * Get the Service Credentials for the Integration Environment
     *
     * @param string $type
     * @return array
     */
    protected function getIntegrationCredentials(string $type = null)
    {
        return include __DIR__ . '/' .
            trim(self::CREDENTIALS_DIR, '/') . '/' .
            trim(self::INTEGRATION_KEYS, '/');
    }

    /*
    |--------------------------------------------------------------------------
    | Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Gets and Acknowledges a WebpayClient in Transbank
     *
     * @param $transaction
     * @param $options
     * @return Contracts\ResponseInterface
     */
    public function getTransaction($transaction, $options = null)
    {
        // Add the `issuedAt` timestamp for getting the transaction
        return parent::getTransaction($transaction + ['issuedAt' => time()], 'onepay.cart');
    }

    /*
    |--------------------------------------------------------------------------
    | Parsers
    |--------------------------------------------------------------------------
    */

    /**
     * Transform the adapter raw answer of a transaction commitment to a
     * more friendly Onepay Response
     *
     * @param array $result
     * @param mixed $options
     * @return Contracts\ResponseInterface
     */
    protected function parseResponse(array $result, $options = null)
    {
        $response = new OnepayResponse($result);

        // Set the type of the WebpayClient where this response belongs
        $response->setType($options);

        // Set the status of the Response
        $response->dynamicallySetSuccessStatus();

        return $response;
    }
}