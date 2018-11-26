<?php

namespace Transbank\Wrapper\WebpaySoap;

use LuisUrrutia\TransbankSoap\Validation;
use Transbank\Wrapper\Connectors\SoapConnector;
use Transbank\Wrapper\Exceptions\Webpay\ErrorResponseException;
use Transbank\Wrapper\Exceptions\Webpay\InvalidWebpayTransactionException;

abstract class Transaction
{
    protected static $ENDPOINTS = [
        'webpay' => [
            'integration'   => 'https://webpay3gint.transbank.cl/WSWebpayTransaction/cxf/WSWebpayService?wsdl',
            'production'    => 'https://webpay3g.transbank.cl/WSWebpayTransaction/cxf/WSWebpayService?wsdl',
        ],
        'commerce' => [
            'integration'   => 'https://webpay3gint.transbank.cl/WSWebpayTransaction/cxf/WSCommerceIntegrationService?wsdl',
            'production'    => 'https://webpay3g.transbank.cl/WSWebpayTransaction/cxf/WSCommerceIntegrationService?wsdl',
        ],
        'complete' => [
            'integration'   => 'https://webpay3gint.transbank.cl/WSWebpayTransaction/cxf/WSCompleteWebpayService?wsdl',
            'production'    => 'https://webpay3g.transbank.cl/WSWebpayTransaction/cxf/WSCompleteWebpayService?wsdl',
        ],
        'oneclick' => [
            'integration'   => 'https://webpay3gint.transbank.cl/webpayserver/wswebpay/OneClickPaymentService?wsdl',
            'production'    => 'https://webpay3g.transbank.cl/webpayserver/wswebpay/OneClickPaymentService?wsdl',
        ],
    ];

    /**
     * Endpoint type to use
     *
     * @var string
     */
    protected $endpointType;

    /**
     * Transbank Endpoint to connect to
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Class map for SOAP
     *
     * @var array
     */
    protected $classMap;

    /**
     * Soap Connector
     *
     * @var SoapConnector
     */
    protected $connector;

    /**
     * Credentials for the Soap Client
     *
     * @var array
     */
    protected $credentials;

    /**
     * If Environment is production (default: no fucking way)
     *
     * @var bool
     */
    protected $isProduction = false;

    /**
     * Transaction constructor.
     *
     * @param bool $isProduction
     * @param array $credentials
     */
    public function __construct(bool $isProduction, array $credentials)
    {
        $this->isProduction = $isProduction;

        $this->credentials = $credentials;

        $this->bootEndpoint();

        $this->bootClassMap();

        $this->bootSoapClient();
    }

    /*
    |--------------------------------------------------------------------------
    | Initialization
    |--------------------------------------------------------------------------
    */

    /**
     * Creates a new instance of the Soap Client using the Configuration as base
     *
     * @return void
     */
    protected function bootSoapClient()
    {
        $this->connector = new SoapConnector(
            $this->endpoint,
            $this->credentials['privateKey'],
            $this->credentials['publicCert'],
            [
                'classmap' => $this->classMap,
                'trace' => !$this->isProduction,
                'exceptions' => true
            ]
        );
    }

    /**
     * Initializes the Class Map to give to the Soap Client
     *
     * @return void
     */
    protected function bootClassMap()
    {
        $this->classMap = include __DIR__ . '/classmaps.php';
    }

    protected function bootEndpoint()
    {
        $this->endpoint = self::$ENDPOINTS[$this->endpointType][$this->isProduction ? 'production' : 'integration'];
    }

    /*
    |--------------------------------------------------------------------------
    | Common functions for all Transactions
    |--------------------------------------------------------------------------
    */

    /**
     * Validates the last response from the SoapClient
     *
     * @return bool
     */
    protected function validate()
    {
        return (new Validation(
            $this->connector->__getLastResponse(),
            $this->credentials['webpayCert']
        ))->isValid();
    }

    /**
     * Returns the current Endpoint in use
     *
     * @return string
     */
    protected function getEndpoint()
    {
        return $this->endpoint;
    }

    /*
    |--------------------------------------------------------------------------
    | Exception handling
    |--------------------------------------------------------------------------
    */

    /**
     * Returns a Validation Error array
     *
     * @throws ErrorResponseException
     */
    protected function throwException()
    {
        throw new ErrorResponseException();
    }

    /**
     * Returns a Connection Error array
     *
     * @param string $message
     * @throws ErrorResponseException
     */
    protected function throwExceptionWithMessage($message)
    {
        $replaceArray = ['<!--' => '', '-->' => ''];
        throw new ErrorResponseException(str_replace(array_keys($replaceArray), array_values($replaceArray), $message));
    }

}
