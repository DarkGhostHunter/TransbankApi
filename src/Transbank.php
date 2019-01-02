<?php

namespace DarkGhostHunter\TransbankApi;

use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use Exception;
use DarkGhostHunter\TransbankApi\Exceptions\Credentials\CredentialInvalidException;
use DarkGhostHunter\TransbankApi\Exceptions\Transbank\InvalidServiceException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class TransbankConfig
 *
 * @package DarkGhostHunter\TransbankApi
 */
class Transbank
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
    protected const AVAILABLE_SERVICES = ['webpay', 'onepay'];

    /**
     * Services holder
     *
     * @var array
     */
    protected $services = [];

    /**
     * Determines if the Environment is Production
     *
     * @var bool
     */
    protected $isProduction = false;

    /**
     * Service Defaults Values for its Transactions
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
     * The Logger instance to log transactions
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Transbank constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Gets a default option for a service by the service name and option
     *
     * @param string $service
     * @param string $option
     * @param null $default
     * @return string|null
     */
    public function getDefault(string $service, string $option, $default = null)
    {
        return $this->servicesDefaults[$service][$option] ?? $default;
    }

    /**
     * Sets a single default option for the service
     *
     * @param string $service
     * @param string $option
     * @param $value
     * @throws InvalidServiceException
     */
    public function setDefault(string $service, string $option, $value)
    {
        if (in_array($service, self::AVAILABLE_SERVICES)) {
            $this->servicesDefaults[$service][$option] = $value;
            return;
        }

        throw new InvalidServiceException($service);
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
     * @return Fluent|null
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
     * @throws CredentialInvalidException
     * @throws InvalidServiceException
     */
    public function setCredentials(string $service, array $credentials)
    {
        // Check if the service is in the available services array
        if (!in_array($service, self::AVAILABLE_SERVICES)) {
            throw new InvalidServiceException($service);
        }

        foreach ($credentials as $credential) {
            if (!is_string($credential)) {
                throw new CredentialInvalidException($service, $credential);
            }
        }

        $this->servicesCredentials[$service] = new Fluent($credentials);

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

    /**
     * Sets the environment for the Transbank Services
     *
     * @param string $environment
     * @return void
     */
    public function setEnvironment(string $environment)
    {
        $this->isProduction = $environment === self::PRODUCTION_ENV;
    }

    /**
     * Get the Logger
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set the Logger
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /*
    |--------------------------------------------------------------------------
    | Services Constructor
    |--------------------------------------------------------------------------
    */

    /**
     * Returns or creates a WebpaySoap instance
     *
     * @return Webpay
     * @throws Exception
     */
    public function webpay()
    {
        return $this->services['webpay'] ?? $this->services['webpay'] = new Webpay($this, $this->logger);
    }

    /**
     * Returns or creates a new Onepay instance
     *
     * @return Onepay
     * @throws Exception
     */
    public function onepay()
    {
        return $this->services['onepay'] ?? $this->services['onepay'] = new Onepay($this, $this->logger);
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
     * @param LoggerInterface $logger
     * @return Transbank
     */
    public static function make(string $environment, array $credentials = [], LoggerInterface $logger = null)
    {
        // Instantiate Transbank with the Logger. If no logger was passed, we will
        // use the default Null Logger which logs nothing.
        $transbank = new static($logger ?? new NullLogger());

        // Set the environment.
        $transbank->setEnvironment($environment);

        // For each credentials array, set them.
        foreach ($credentials as $service => $credentialBag) {
            $transbank->setCredentials($service, $credentialBag);
        }

        return $transbank;
    }

}