<?php

namespace Transbank\Wrapper\Contracts;

interface TransactionInterface
{
    /**
     * Set default attributes for the Item
     *
     * @param array $defaults
     */
    public function setDefaults(array $defaults);

    /**
     * Set the Transaction type
     *
     * @param string $type
     */
    public function setType(string $type);

    /**
     * Return the Transaction type
     *
     * @return string
     */
    public function getType();

    /**
     * Sets the Service to be used for this Transaction
     *
     * @param ServiceInterface $service
     */
    public function setService(ServiceInterface $service);

    /**
     * Returns the Service used by this Transaction
     *
     * @return ServiceInterface
     */
    public function getService();

    /**
     * Commits the transaction to Transbank and return a AbstractResult
     *
     * @return \Transbank\Wrapper\Results\ServiceResult
     */
    public function getResult();

    /**
     * Forcefully commits the transaction to Transbank
     *
     * @return \Transbank\Wrapper\Results\ServiceResult
     */
    public function forceGetResult();

    /**
     * Serializes the object to a string
     *
     * @return string
     */
    public function __toString();
}