<?php

namespace Transbank\Wrapper;

use BadMethodCallException;
use Exception;
use Transbank\Wrapper\Contracts\AdapterInterface;
use Transbank\Wrapper\Contracts\ServiceInterface;
use Transbank\Wrapper\Contracts\TransactionInterface;
use Transbank\Wrapper\Helpers\Helpers;
use Transbank\Wrapper\Results\ServiceResult;
use Transbank\Wrapper\Transactions\ServiceTransaction;

/**
 * Class AbstractService
 * @package Transbank\Wrapper
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
     * @var TransbankConfig
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
     * @var array
     */
    protected $credentials = [];

    /**
     * Class in charge of dispatching a Transaction
     *
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Transaction Factory to use for forwarding calls
     *
     * @var TransactionFactories\TransactionFactory
     */
    protected $factory;

    /**
     * Webpay constructor.
     *
     * @param TransbankConfig $transbankConfig
     * @throws \Exception
     */
    public function __construct(TransbankConfig $transbankConfig)
    {
        $this->transbankConfig = $transbankConfig;

        $this->defaults = $this->getDefaults();
        $this->credentials = $this->getCredentials();

        $this->boot();
    }

    /**
     * Boot any logic needed for the Service, like the Adapter and Factory;
     *
     * @return void
     */
    public function boot()
    {
        $this->bootAdapter();
        $this->bootFactory();
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
    abstract public function bootFactory();

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
     * Returns the service Credentials to use with the Adapter
     *
     * @return null
     */
    protected function getCredentials()
    {
        return $this->transbankConfig->getCredentials(
            lcfirst(Helpers::classBasename(static::class))
        ) ?? [];
    }

    /**
     * Get the Service Credentials for the Integration Environment
     *
     * @param ServiceTransaction $transaction
     * @return array
     */
    abstract protected function getIntegrationCredentials(ServiceTransaction $transaction);

    /**
     * Get the Service Credentials for the Production Environment
     *
     * @param ServiceTransaction $transaction
     * @return array
     */
    abstract protected function getProductionCredentials(ServiceTransaction $transaction);

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
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Set Factory
     *
     * @param string $factory
     */
    public function setFactory(string $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Performs a Transaction into Transbank Services using the Adapter
     *
     * @param TransactionInterface $transaction
     * @return ServiceResult
     */
    public function commitTransaction(TransactionInterface $transaction)
    {
        // Set the adapter using the application keys with the service keys.
        // The application issued credentials will have precedence, though.
        $this->adapter->setCredentials(
            array_merge(
                $this->isProduction()
                    ? $this->getProductionCredentials($transaction)
                    : $this->getIntegrationCredentials($transaction),
                $this->getCredentials() ?? []
            )
        );

        // Commit the transaction to the adapter
        return $this->parseToTransactionResult(
            $this->adapter->commit($transaction)
        );
    }

    /**
     * Gets and Acknowledges a Transaction in Transbank
     *
     * @param $transaction
     * @return ServiceResult
     */
    public function confirmTransaction($transaction)
    {
        return $this->parseToTransactionResult(
            $this->adapter->confirm($transaction)
        );
    }

    /**
     * Transform the adapter raw Result to a Transaction Result
     *
     * @param $result
     * @return ServiceResult
     */
    abstract protected function parseToTransactionResult($result);

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
        // Proceed to call the Factory, or throw an Exception if the method doesn't exists.
        if (method_exists($this->factory, $method) || is_callable([$this->factory, $method])) {
            return $this->factory->{$method}(...$parameters);
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

    /**
     * Returns a new service instance using the Transbank Configuration
     *
     * @param TransbankConfig $config
     * @return AbstractService|$this
     * @throws \Exception
     */
    public static function fromConfig(TransbankConfig $config)
    {
        return new static($config);
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
}