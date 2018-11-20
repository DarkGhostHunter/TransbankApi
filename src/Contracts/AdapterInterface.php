<?php

namespace Transbank\Wrapper\Contracts;


/**
 * Interface AdapterInterface
 *
 * Provides methods to use against the Transbank SDK depending on the transaction properties
 * and attributes.
 *
 * @package Transbank\Wrapper\Contracts
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
    public function commit(TransactionInterface $transaction, array $options = []);

    /**
     * Retrieves and Acknowledges a transaction into the Transbank SDK
     *
     * @param $transaction
     * @param array $options
     * @return mixed
     */
    public function confirm($transaction, array $options = []);

    /**
     * Return the Error Code from the Response
     *
     * @return mixed
     */
    public function getErrorCode() : string;

    /**
     * Translates the Error Code to a humanized string
     *
     * @return string
     */
    public function getErrorForHumans() : string;

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