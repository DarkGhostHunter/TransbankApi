<?php

namespace DarkGhostHunter\TransbankApi\Adapters;

use DarkGhostHunter\TransbankApi\Contracts\AdapterInterface;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * Determines if the environment is Production
     *
     * @var bool
     */
    protected $isProduction = false;

    /**
     * Credentials for the Service and Transaction
     *
     * @var Fluent
     */
    protected $credentials;

    /**
     * Sets credentials to use against Transbank SDK
     *
     * @param Fluent $credentials
     * @return mixed
     */
    public function setCredentials(Fluent $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * Returns if the environment is Production
     *
     * @return bool
     */
    public function isProduction()
    {
        return $this->isProduction;
    }

    /**
     * Sets if the the environment is Production
     *
     * @param bool $isProduction
     */
    public function setIsProduction(bool $isProduction)
    {
        $this->isProduction = $isProduction;
    }
}