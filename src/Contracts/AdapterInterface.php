<?php

namespace DarkGhostHunter\TransbankApi\Contracts;


/**
 * Interface AdapterInterface
 *
 * Provides methods to use against the Transbank SDK depending on the transaction properties
 * and attributes.
 *
 * @package DarkGhostHunter\TransbankApi\Contracts
 */
interface AdapterInterface
{
    /**
     * Sets credentials to use against Transbank SDK
     *
     * @param array $credentials
     * @return mixed
     */
    public function setCredentials(array $credentials);

    /**
     * Commits a transaction into the Transbank SDK
     *
     * @param TransactionInterface $transaction
     * @param array $options
     * @return mixed
     */
    public function commit(TransactionInterface $transaction, $options = null);

    /**
     * Retrieves and Confirms a transaction into the Transbank SDK
     *
     * @param $transaction
     * @param array $options
     * @return mixed
     */
    public function get($transaction, $options = null);

    /**
     * Returns if the environment is Production
     *
     * @return bool
     */
    public function isProduction();

    /**
     * Sets if the the environment is Production
     *
     * @param bool $isProduction
     */
    public function setIsProduction(bool $isProduction);
}