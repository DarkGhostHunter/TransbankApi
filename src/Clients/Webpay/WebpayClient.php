<?php

namespace DarkGhostHunter\TransbankApi\Clients\Webpay;

use DarkGhostHunter\TransbankApi\Clients\AbstractClient;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use LuisUrrutia\TransbankSoap\Validation;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException;

abstract class WebpayClient extends AbstractClient
{
    /**
     * Endpoints for every transaction type
     *
     * @var array
     */
    protected static $endpoints = [
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
     * Class map for SOAP
     *
     * @var array
     */
    protected $classMap;

    /**
     * Soap Connector
     *
     * @var SoapImplementation
     */
    protected $connector;


    /*
    |--------------------------------------------------------------------------
    | Initialization
    |--------------------------------------------------------------------------
    */

    /**
     * Boot the connector
     *
     * @return void
     */
    protected function boot()
    {
        $this->bootEndpoint();

        $this->bootClassMap();

        $this->bootSoapClient();
    }

    /**
     * Creates a new instance of the Soap Client using the Configuration as base
     *
     * @return void
     */
    protected function bootSoapClient()
    {
        $this->connector = new SoapImplementation(
            $this->endpoint,
            $this->credentials->privateKey,
            $this->credentials->publicCert,
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

    /**
     * Sets the Endpoint to use depending on the environment type
     *
     * @return void
     */
    protected function bootEndpoint()
    {
        $this->endpoint = self::$endpoints[$this->endpointType][$this->isProduction ? 'production' : 'integration'];
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
            $this->credentials->webpayCert
        ))->isValid();
    }

}
