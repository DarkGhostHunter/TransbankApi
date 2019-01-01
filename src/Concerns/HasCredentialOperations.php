<?php

namespace DarkGhostHunter\TransbankApi\Concerns;

use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\Helpers\Helpers;

trait HasCredentialOperations
{


    /*
    |--------------------------------------------------------------------------
    | Credentials Operations
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the service Credentials to use with the Adapter
     *
     * @return null
     */
    protected function getCredentials()
    {
        return $this->transbankConfig->getCredentials(
            lcfirst(Helpers::classBasename(static::class))
        );
    }

    /**
     * Returns the credentials directory for the active environment
     *
     * @return string
     */
    public function environmentCredentialsDirectory()
    {
        return $this->credentialsDirectory() . '/' . $this->transbankConfig->getEnvironment() . '/';
    }

    /**
     * Set the correct credentials in the adapter.
     *
     * Whe using `integration` environments, Credentials may depend on the
     * transaction type being used, so the type is passed to the method.
     * After that, these are overridden by any of the user credentials.
     *
     * @param string|null $type
     */
    protected function setAdapterCredentials(string $type = null)
    {
        $this->adapter->setCredentials(
            new Fluent(
                array_merge(
                    $this->isProduction()
                        ? $this->getProductionCredentials()
                        : $this->getIntegrationCredentials($type),
                    (array)$this->credentials ?? []
                )
            )
        );
    }

    /**
     * Get the Service Credentials for the Production Environment
     *
     * @return array
     */
    abstract protected function getProductionCredentials();

    /**
     * Get the Service Credentials for the Integration Environment
     *
     * @param string $type
     * @return array
     */
    abstract protected function getIntegrationCredentials(string $type = null);
}