<?php

namespace DarkGhostHunter\TransbankApi\Clients;

use DarkGhostHunter\TransbankApi\Helpers\Fluent;

abstract class AbstractClient
{
    /**
     * If Environment is production (default: no)
     *
     * @var bool
     */
    protected $isProduction = false;

    /**
     * Credentials for the Service Client
     *
     * @var Fluent
     */
    protected $credentials;

    /**
     * Service Endpoint to connect to
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Connector constructor.
     *
     * @param bool $isProduction
     * @param Fluent $credentials
     */
    public function __construct(bool $isProduction, Fluent $credentials)
    {
        $this->isProduction = $isProduction;
        $this->credentials = $credentials;
    }

    /**
     * Get the Endpoint
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set the Endpoint
     *
     * @param string $endpoint
     */
    public function setEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Boot the connector
     *
     * @return void
     */
    abstract protected function boot();
}