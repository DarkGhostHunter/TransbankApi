<?php

namespace DarkGhostHunter\TransbankApi;

use DarkGhostHunter\TransbankApi\Contracts\AdapterInterface;
use DarkGhostHunter\TransbankApi\Contracts\ServiceInterface;
use DarkGhostHunter\TransbankApi\Contracts\TransactionInterface;
use DarkGhostHunter\TransbankApi\Helpers\Helpers;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\ResponseFactories\AbstractResponseFactory;
use DarkGhostHunter\TransbankApi\TransactionFactories\AbstractTransactionFactory;
use Exception;
use BadMethodCallException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class AbstractService
 * @package DarkGhostHunter\TransbankApi
 */
abstract class AbstractService implements ServiceInterface
{
    use Concerns\HasCredentialOperations;

    /**
     * Credentials Location for the Service
     *
     * @const string
     */
    protected const CREDENTIALS_DIR = '../credentials/';

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
    protected $responseFactory;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    protected $logger;

    /*
    |--------------------------------------------------------------------------
    | Construct
    |--------------------------------------------------------------------------
    */

    /**
     * Abstract Service constructor.
     *
     * @param Transbank $transbankConfig
     * @param LoggerInterface $logger
     */
    public function __construct(Transbank $transbankConfig, LoggerInterface $logger)
    {
        $this->transbankConfig = $transbankConfig;
        $this->logger = $logger;

        $this->defaults = $this->getDefaults();
        $this->credentials = $this->getCredentials();

        $this->boot();
    }

    /*
    |--------------------------------------------------------------------------
    | Credentials Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the credentials directory
     *
     * @return string
     */
    public function credentialsDirectory()
    {
        return __DIR__ . '/' . trim(self::CREDENTIALS_DIR, '/');
    }

    /*
    |--------------------------------------------------------------------------
    | Booting
    |--------------------------------------------------------------------------
    */

    /**
     * Boot any logic needed for the Service, like the Adapter and Transaction Factory;
     *
     * @return void
     */
    protected function boot()
    {
        $this->bootAdapter();
        $this->bootTransactionFactory();
    }

    /**
     * Instantiates (and/or boots) the Adapter for the Service
     *
     * @return void
     */
    abstract protected function bootAdapter();

    /**
     * Instantiates (and/or boots) the Transaction Factory for the Service
     *
     * @return void
     */
    abstract protected function bootTransactionFactory();

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
     * Get the Transaction Factory
     *
     * @return string
     */
    public function getTransactionFactory()
    {
        return $this->transactionFactory;
    }

    /**
     * Set the Transaction Factory
     *
     * @param AbstractTransactionFactory $transactionFactory
     */
    public function setTransactionFactory(AbstractTransactionFactory $transactionFactory)
    {
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * Get the Response Factory
     *
     * @return string
     */
    public function getResponseFactory()
    {
        return $this->responseFactory;
    }

    /**
     * Set the Response Factory
     *
     * @param AbstractResponseFactory $responseFactory
     */
    public function setResponseFactory(AbstractResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

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
        $this->setAdapterCredentials($type = $transaction->getType());

        $this->logger->info("Getting [$type]: $transaction");

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
    public function getTransaction($transaction, $options = null)
    {
        // Set the correct adapter credentials
        $this->setAdapterCredentials($options);

        $this->logger->info("Getting [$options]: " . json_encode($transaction));

        return $this->parseResponse(
            $this->adapter->retrieveAndConfirm($transaction, $options),
            $options
        );
    }

    /**
     * Get the Logger
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set the Logger
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /*
    |--------------------------------------------------------------------------
    | Parser
    |--------------------------------------------------------------------------
    */

    /**
     * Transform the adapter raw answer for a transaction to a more friendly
     * Transbank Service Response
     *
     * @param array $result
     * @param mixed $options
     * @return Contracts\ResponseInterface
     */
    abstract protected function parseResponse(array $result, $options = null);

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

        // Try the same with the Response factory
        if (method_exists($this->responseFactory, $method) && is_callable([$this->responseFactory, $method])) {
            return $this->responseFactory->{$method}(...$parameters);
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
     * @param LoggerInterface|null $logger
     * @return AbstractService|$this
     */
    public static function fromConfig(Transbank $config, LoggerInterface $logger = null)
    {
        return new static($config, $logger ?? new NullLogger());
    }
}