<?php

namespace DarkGhostHunter\TransbankApi;

use DarkGhostHunter\TransbankApi\Contracts\AdapterInterface;
use DarkGhostHunter\TransbankApi\Contracts\ServiceInterface;
use DarkGhostHunter\TransbankApi\Contracts\TransactionInterface;
use DarkGhostHunter\TransbankApi\Helpers\Helpers;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use Exception;
use BadMethodCallException;

/**
 * Class AbstractService
 * @package DarkGhostHunter\TransbankApi
 */
abstract class AbstractService implements ServiceInterface
{
    /**
     * Credentials Location for the Service
     *
     * @const string
     */
    protected const CREDENTIALS_DIR = '/../credentials/';

    /**
     * Transbank Configuration to use for the Service
     *
     * @var Transbank
     */
    protected $transbankConfig;

    /**
     * Default options to set for new Transactions
     *
     * @var array
     */
    protected $defaults = [];

    /**
     * Credentials to use with the Adapter
     *
     * @var \DarkGhostHunter\TransbankApi\Helpers\Fluent
     */
    protected $credentials;

    /**
     * Class in charge of dispatching a Transaction
     *
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Transaction Factory to use for forwarding calls
     *
     * @var TransactionFactories\AbstractTransactionFactory
     */
    protected $transactionFactory;

    /**
     * Result Factory to use for forwarding calls
     *
     * @var ResponseFactories\AbstractResponseFactory
     */
    protected $resultFactory;

    /*
    |--------------------------------------------------------------------------
    | Construct
    |--------------------------------------------------------------------------
    */

    /**
     * WebpaySoap constructor.
     *
     * @param Transbank $transbankConfig
     * @throws \Exception
     */
    public function __construct(Transbank $transbankConfig)
    {
        $this->transbankConfig = $transbankConfig;

        $this->defaults = $this->getDefaults();
        $this->credentials = $this->getCredentials();

        $this->boot();
    }

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
    abstract public function bootAdapter();

    /**
     * Instantiates (and/or boots) the Transaction Factory for the Service
     *
     * @return void
     */
    abstract public function bootTransactionFactory();

    /**
     * Instantiates (and/or boots) the Result Factory for the Service
     *
     * @return void
     */
    abstract public function bootResponseFactory();

    /*
    |--------------------------------------------------------------------------
    | Getters and Setters
    |--------------------------------------------------------------------------
    */

    /**
     * Returns if the service is using a Production environment
     *
     * @return bool
     */
    public function isProduction()
    {
        return $this->transbankConfig->isProduction();
    }

    /**
     * Returns if the service is using an Integration environment
     *
     * @return bool
     */
    public function isIntegration()
    {
        return $this->transbankConfig->isIntegration();
    }

    /**
     * Retrieves the default options for the service Transactions
     *
     * @return array|null
     */
    protected function getDefaults()
    {
        return $this->transbankConfig->getDefaults(
                lcfirst(Helpers::classBasename(static::class))
            ) ?? [];
    }

    /**
     * Get the Adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Set the Adapter
     *
     * @param AdapterInterface $adapter
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Get Factory
     *
     * @return string
     */
    public function getTransactionFactory()
    {
        return $this->transactionFactory;
    }

    /**
     * Set Factory
     *
     * @param string $transactionFactory
     */
    public function setTransactionFactory(string $transactionFactory)
    {
        $this->transactionFactory = $transactionFactory;
    }

    /*
    |--------------------------------------------------------------------------
    | Credentials Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the service Credentials to use with the Adapter
     *
     * @return null
     */
    protected function getCredentials()
    {
        return $this->transbankConfig->getCredentials(
            lcfirst(Helpers::classBasename(static::class))
        );
    }

    /**
     * Returns the credentials directory
     *
     * @return string
     */
    protected function credentialsDirectory()
    {
        return __DIR__ . self::CREDENTIALS_DIR . '/';
    }

    /**
     * Returns de credentials directory for the active environment
     *
     * @return string
     */
    protected function environmentCredentialsDirectory()
    {
        return $this->credentialsDirectory() . $this->transbankConfig->getEnvironment() . '/';
    }

    /**
     * Set the correct credentials in the adapter.
     *
     * Whe using `integration` environments, Credentials may depend on the
     * transaction type being used, so the type is passed to the method.
     * After that, these are overridden by any of the user credentials.
     *
     * @param string|null $type
     */
    protected function setAdapterCredentials(string $type = null)
    {
        $this->adapter->setCredentials(
            new Fluent(
                array_merge(
                    $this->isProduction()
                        ? $this->getProductionCredentials()
                        : $this->getIntegrationCredentials($type),
                    (array)$this->credentials ?? []
                )
            )
        );
    }

    /**
     * Get the Service Credentials for the Production Environment
     *
     * @return array
     */
    abstract protected function getProductionCredentials();

    /**
     * Get the Service Credentials for the Integration Environment
     *
     * @param string $type
     * @return array
     */
    abstract protected function getIntegrationCredentials(string $type = null);

    /*
    |--------------------------------------------------------------------------
    | Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Performs a Transaction into Transbank Services using the Adapter
     *
     * @param TransactionInterface $transaction
     * @return Contracts\ResponseInterface
     */
    public function commit(TransactionInterface $transaction)
    {
        // Set the correct adapter credentials
        $this->setAdapterCredentials($transaction->getType());

        // Commit the transaction to the adapter
        return $this->parseResponse(
            $this->adapter->commit($transaction),
            $transaction->getType()
        );
    }

    /**
     * Gets and Acknowledges a Transaction in Transbank
     *
     * @param $transaction
     * @param $options
     * @return Contracts\ResponseInterface
     */
    public function get($transaction, $options = null)
    {
        // Set the correct adapter credentials
        $this->setAdapterCredentials($options);

        return $this->parseResponse(
            $this->adapter->get($transaction, $options),
            $options
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Parser
    |--------------------------------------------------------------------------
    */

    /**
     * Transform the adapter raw answer of a transaction commitment to a
     * more friendly Webpay Response
     *
     * @param array $result
     * @param string $type
     * @return Contracts\ResponseInterface
     */
    abstract protected function parseResponse(array $result, string $type);

    /*
    |--------------------------------------------------------------------------
    | Internal methods
    |--------------------------------------------------------------------------
    */

    /**
     * Dynamically handle class to the Transaction Factory from __class
     *
     * @param $method
     * @param $parameters
     * @return mixed
     * @throws Exception|\ReflectionException
     */
    protected function forwardCallToTransactionFactory($method, $parameters)
    {
        // Proceed to call the Transaction Factory, or throw an Exception if the method doesn't exists.
        if (method_exists($this->transactionFactory, $method) && is_callable([$this->transactionFactory, $method])) {
            return $this->transactionFactory->{$method}(...$parameters);
        }

        // Try the same with the result factory
        if (method_exists($this->resultFactory, $method) && is_callable([$this->resultFactory, $method])) {
            return $this->resultFactory->{$method}(...$parameters);
        }

        throw new BadMethodCallException(
            "Method $method does not exist in class " . Helpers::classBasename(static::class)
        );
    }

    /**
     * Dynamically forwards calls to the Transaction Factory
     *
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    public function __call($name, $arguments)
    {
        return $this->forwardCallToTransactionFactory($name, $arguments);
    }

    /*
    |--------------------------------------------------------------------------
    | Static instancing
    |--------------------------------------------------------------------------
    */

    /**
     * Returns a new service instance using the Transbank Configuration
     *
     * @param Transbank $config
     * @return AbstractService|$this
     * @throws \Exception
     */
    public static function fromConfig(Transbank $config)
    {
        return new static($config);
    }
}