<?php

namespace DarkGhostHunter\TransbankApi\Clients;

use DarkGhostHunter\TransbankApi\Helpers\Fluent;

abstract class AbstractConnector
{

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

        $this->boot();
    }

    /**
     * Boot the connector
     *
     * @return mixed
     */
    abstract public function boot();
}