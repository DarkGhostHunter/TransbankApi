<?php

namespace DarkGhostHunter\TransbankApi\Contracts;

use DarkGhostHunter\TransbankApi\Responses\AbstractResponse;
use DarkGhostHunter\TransbankApi\Transbank;
use Psr\Log\LoggerInterface;

/**
 * Interface TransbankServiceInterface
 *
 * This is the name of the Service available in Transbank. Works a pivot between
 * the user transactions requests and Transbank responses. It must receive a
 * Transbank Configuration and use that to connect to Transbank Services.
 *
 * @package DarkGhostHunter\TransbankApi\Contracts
 */
interface ServiceInterface
{
    /**
     * TransbankServiceInterface constructor.
     *
     * @param Transbank $config
     * @param LoggerInterface $logger
     */
    public function __construct(Transbank $config, LoggerInterface $logger);

    /**
     * Returns if the service is using a Production environment
     *
     * @return bool
     */
    public function isProduction();

    /**
     * Returns if the service is using an Integration environment
     *
     * @return bool
     */
    public function isIntegration();

    /**
     * Get the Adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter();

    /**
     * Set the Adapter
     *
     * @param AdapterInterface $adapter
     */
    public function setAdapter(AdapterInterface $adapter);

    /**
     * Performs a new Transaction to Transbank Services and returns its Result
     *
     * @param TransactionInterface $transaction
     * @return AbstractResponse
     */
    public function commit(TransactionInterface $transaction);

    /**
     * Gets and Acknowledges a Transaction in Transbank
     *
     * @param $transaction
     * @param $options
     * @return AbstractResponse
     */
    public function getTransaction($transaction, $options = null);

    /**
     * Returns a new service instance using the Transbank Configuration
     *
     * @param Transbank $config
     * @param LoggerInterface|null $logger
     * @return ServiceInterface|$this
     */
    public static function fromConfig(Transbank $config, LoggerInterface $logger = null);
}