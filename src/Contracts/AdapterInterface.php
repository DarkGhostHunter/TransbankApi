<?php

namespace DarkGhostHunter\TransbankApi\Contracts;

use DarkGhostHunter\TransbankApi\Helpers\Fluent;

/**
 * Interface AdapterInterface
 *
 * Provides methods to use against a Transbank Service depending on the transaction properties
 * and credentials of the Service this eventually belong. So you can swap the way to interact
 * with Transbank without having to change public-facing methods.
 *
 * @package DarkGhostHunter\TransbankApi\Contracts
 */
interface AdapterInterface
{
    /**
     * Sets credentials to use against Transbank SDK
     *
     * @param Fluent $credentials
     * @return mixed
     */
    public function setCredentials(Fluent $credentials);

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
     * @param string|array|TransactionInterface $transaction
     * @param string|array $options
     * @return mixed
     */
    public function getAndConfirm($transaction, $options = null);

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