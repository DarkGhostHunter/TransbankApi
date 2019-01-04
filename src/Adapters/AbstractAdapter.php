<?php

namespace DarkGhostHunter\TransbankApi\Adapters;

use DarkGhostHunter\Fluid\Fluid;
use DarkGhostHunter\TransbankApi\Contracts\AdapterInterface;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * Determines if the environment is Production
     *
     * @var bool
     */
    protected $isProduction = false;

    /**
     * Service Client holder
     *
     * @var object
     */
    protected $client;

    /**
     * Credentials for the Service and Transaction
     *
     * @var Fluid
     */
    protected $credentials;

    /**
     * Sets a Client to communicate with Transbank
     *
     * @param $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * Returns the Client used to communicate with Transbank
     *
     * @return object
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Sets credentials to use against Transbank SDK
     *
     * @param Fluid $credentials
     * @return mixed
     */
    public function setCredentials(Fluid $credentials)
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