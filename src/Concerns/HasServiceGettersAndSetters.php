<?php

namespace DarkGhostHunter\TransbankApi\Concerns;

use DarkGhostHunter\TransbankApi\Contracts\AdapterInterface;
use DarkGhostHunter\TransbankApi\Helpers\Helpers;
use DarkGhostHunter\TransbankApi\ResponseFactories\AbstractResponseFactory;
use DarkGhostHunter\TransbankApi\TransactionFactories\AbstractTransactionFactory;

trait HasServiceGettersAndSetters
{
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
}