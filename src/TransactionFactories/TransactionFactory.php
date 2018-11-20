<?php

namespace Transbank\Wrapper\TransactionFactories;

use Transbank\Wrapper\AbstractService;
use Transbank\Wrapper\Transactions\ServiceTransaction;

abstract class TransactionFactory
{
    /**
     * Service to inject into the Transactions
     *
     * @var AbstractService
     */
    protected $service;

    /**
     * Defaults to inject into the Transactions
     *
     * @var array
     */
    protected $defaults;

    /**
     * TransactionFactory constructor.
     * @param AbstractService $service
     * @param array $defaults
     */
    public function __construct(AbstractService $service, array $defaults = [])
    {
        $this->service = $service;
        $this->defaults = $defaults;
    }

    /**
     * Returns an instance of a Transaction
     *
     * @param string $type
     * @param array $attributes
     * @return ServiceTransaction
     */
    abstract protected function makeTransaction(string $type, array $attributes = []);

    /**
     * Injects Service, Defaults and Type to the Transaction
     *
     * @param string $type
     * @param ServiceTransaction $transaction
     * @return ServiceTransaction
     */
    protected function prepareTransaction(string $type, ServiceTransaction $transaction)
    {
        // The Type of the transaction, so the Adapter can differentiate how
        // to commit it to Transbank if there is more than one.
        $transaction->setType($type);

        // Setting the services allows to commit it using the Service's Adapter.
        $transaction->setService($this->service);

        // If it has defaults, they will be appended
        $transaction->setDefaults($this->defaults);

        return $transaction;
    }

}