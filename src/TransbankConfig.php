<?php

namespace Transbank\Wrapper;

use Exception;
use Transbank\Wrapper\Exceptions\Transbank\InvalidServiceException;

/**
 * Class TransbankConfig
 *
 * @package Transbank\Wrapper
 */
class TransbankConfig
{
    /**
     * Magic word to match for production environments
     *
     * @const string
     */
    protected const PRODUCTION_ENV = 'production';

    /**
     * Magic word to match for integration environments
     *
     * @const string
     */
    protected const INTEGRATION_ENV = 'integration';

    /**
     * Available services for this Transbank wrapper
     *
     * @const array
     */
    protected const AVAILABLE_SERVICES = [
        'webpay',
        'onepay',
    ];

    /**
     * Determines if the Environment is Production
     *
     * @var bool
     */
    protected $isProduction = false;

    /**
     * Service Configurations
     *
     * @var array
     */
    protected $servicesDefaults = [];

    /**
     * Service Credentials
     *
     * @var array
     */
    protected $servicesCredentials = [];

    /**
     * TransbankConfig constructor.
     *
     * @param string $environment
     * @param array $credentials
     * @throws Exception
     */
    public function __construct(string $environment = null, array $credentials = [])
    {
        // Set default as `integration` unless explicitly says production.
        $this->isProduction = $environment === self::PRODUCTION_ENV;

        // Add the Credentials
        foreach ($credentials as $service => $array) {
            $this->setCredentials($service, $array);
        }
    }

    /**
     * Gets a default option for a service by the service name and option
     *
     * @param string $service
     * @param string $option
     * @return string|null
     */
    public function getDefault(string $service, string $option)
    {
        return $this->servicesDefaults[$service][$option] ?? null;
    }

    /**
     * Gets all the Service Default Options
     *
     * @param string $service
     * @return array|null
     */
    public function getDefaults(string $service)
    {
        return $this->servicesDefaults[$service] ?? null;
    }

    /**
     * Set Defaults options for a service
     *
     * @param string $service
     * @param array $defaults
     * @throws Exception
     */
    public function setDefaults(string $service, array $defaults)
    {
        if (in_array($service, self::AVAILABLE_SERVICES)) {
            $this->servicesDefaults[$service] = $defaults;
            return;
        }

        throw new InvalidServiceException($service);
    }

    /**
     * Gets a Service credentials
     *
     * @param string $service
     * @return null
     */
    public function getCredentials(string $service)
    {
        return $this->servicesCredentials[$service] ?? null;
    }

    /**
     * Set Credentials for a service
     *
     * @param string $service
     * @param array $credentials
     * @return void
     * @throws Exception
     */
    public function setCredentials(string $service, array $credentials)
    {
        if (in_array($service, self::AVAILABLE_SERVICES)) {
            $this->servicesCredentials[$service] = $credentials;
            return;
        }

        throw new InvalidServiceException($service);
    }

    /**
     * Returns if the Transbank environment is integration
     *
     * @return bool
     */
    public function isIntegration()
    {
        return !$this->isProduction();
    }

    /**
     * Returns if the Transbank environment is production
     *
     * @return bool
     */
    public function isProduction()
    {
        return $this->isProduction;
    }

    /**
     * Returns the active environment for this TransbankConfig instance
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->isProduction ? self::PRODUCTION_ENV : self::INTEGRATION_ENV;
    }

    /*
    |--------------------------------------------------------------------------
    | Static instantiation
    |--------------------------------------------------------------------------
    */

    /**
     * Creates a new TransbankConfig instance
     *
     * @param string|null $environment
     * @param array $credentials
     * @return TransbankConfig
     * @throws Exception
     */
    public static function environment(string $environment = null, array $credentials = [])
    {
        return new static($environment, $credentials);
    }

}