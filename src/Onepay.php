<?php

namespace Transbank\Wrapper;

use Transbank\Wrapper\Adapters\OnepayAdapter;
use Transbank\Wrapper\Results\ServiceResult;
use Transbank\Wrapper\Results\OnepayResult;
use Transbank\Wrapper\TransactionFactories\OnepayTransactionFactory;
use Transbank\Wrapper\Transactions\ServiceTransaction;

/**
 * Class Onepay
 * @package Transbank\Wrapper
 *
 * @method Transactions\OnepayTransaction|Transactions\OnepayTransaction makeCart(array $items = [])
 * @method Results\OnepayResult createCart(array $items = [])
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
     * Class in charge of dispatching a Transaction
     *
     * @var OnepayAdapter
     */
    protected $adapter;

    /**
     * Transaction Factory to use for forwarding calls
     *
     * @example \Transbank\Wrapper\Webpay\TransactionFactory::class
     * @var string
     */
    protected $factory;

    /**
     * Boot any logic needed for the Service, like the Adapter and Factory;
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
    public function bootFactory()
    {
        $this->factory = new OnepayTransactionFactory($this, $this->defaults);
    }

    /**
     * Get the Service Credentials for the Integration Environment
     *
     * @param ServiceTransaction $transaction
     * @return array
     */
    protected function getIntegrationCredentials(ServiceTransaction $transaction)
    {
        /** @noinspection PhpIncludeInspection */
        return include __DIR__ . self::CREDENTIALS_DIR . self::INTEGRATION_KEYS;
    }

    /**
     * Get the Service Credentials for the Production Environment
     *
     * @param ServiceTransaction $transaction
     * @return array
     */
    protected function getProductionCredentials(ServiceTransaction $transaction)
    {
        /** @noinspection PhpIncludeInspection */
        return include __DIR__ . self::CREDENTIALS_DIR . self::PRODUCTION_KEYS;
    }

    /**
     * Transform the adapter raw Result to a Transaction Result
     *
     * @param $result
     * @return ServiceResult
     */
    protected function parseToTransactionResult($result)
    {
        return new OnepayResult($result);
    }

    /**
     * Gets and Acknowledges a Transaction in Transbank
     *
     * @param $transaction
     * @return ServiceResult
     */
    public function confirmTransaction($transaction)
    {
        // TODO: Implement confirmTransaction() method.
    }
}