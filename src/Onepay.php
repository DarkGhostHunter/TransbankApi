<?php

namespace Transbank\Wrapper;

use Transbank\Wrapper\Adapters\OnepayAdapter;
use Transbank\Wrapper\ResponseFactories\OnepayResponseFactory;
use Transbank\Wrapper\Responses\OnepayResponse;
use Transbank\Wrapper\TransactionFactories\OnepayTransactionFactory;

/**
 * Class Onepay
 * @package Transbank\Wrapper
 *
 * @method Transactions\OnepayTransaction   makeCart(array $attributes = [])
 * @method Responses\OnepayResponse         createCart(array $attributes = [])
 * @method Transactions\OnepayTransaction   makeNullify(array $attributes = [])
 * @method Responses\OnepayResponse         createNullify(array $attributes = [])
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
     * Transaction Factory to use for forwarding calls
     *
     * @example \Transbank\Wrapper\WebpaySoap\AbstractTransactionFactory::class
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
        $this->bootResponseFactory();
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
     * Instantiates (and/or boots) the Transaction Factory for the Service
     *
     * @return void
     */
    public function bootTransactionFactory()
    {
        $this->transactionFactory = new OnepayTransactionFactory($this, $this->defaults);
    }

    /**
     * Instantiates (and/or boots) the Result Factory for the Service
     *
     * @return void
     */
    public function bootResponseFactory()
    {
        $this->resultFactory = new OnepayResponseFactory($this);
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
        return include __DIR__ . self::CREDENTIALS_DIR . self::PRODUCTION_KEYS;
    }

    /**
     * Get the Service Credentials for the Integration Environment
     *
     * @param string $type
     * @return array
     */
    protected function getIntegrationCredentials(string $type = null)
    {
        return include __DIR__ . self::CREDENTIALS_DIR . self::INTEGRATION_KEYS;
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
     * @param $options
     * @return Contracts\ResponseInterface
     */
    public function get($transaction, $options = null)
    {
        return parent::get([$transaction['occ'], $transaction['externalUniqueNumber']], 'onepay.cart');
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
     * @param string $type
     * @return Contracts\ResponseInterface
     */
    protected function parseResponse(array $result, string $type)
    {
        $response = new OnepayResponse($result);

        $response->setType($type);

        // Set the status of the Response
        $response->setStatus();

        return $response;
    }
}