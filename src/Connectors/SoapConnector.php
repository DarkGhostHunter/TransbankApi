<?php

namespace DarkGhostHunter\TransbankApi\Connectors;

use DOMDocument;
use LuisUrrutia\TransbankSoap\Process;
use SoapClient;

/**
 * Class SoapConnector
 * @package DarkGhostHunter\TransbankApi\Connectors
 *
 * @method object acknowledgeTransaction($transaction)
 * @method object getTransactionResult($transaction)
 * @method object initTransaction($transaction)
 *
 *
 */
class SoapConnector extends SoapClient
{

    /**
     * Commerce Private Key
     *
     * @var string
     */
    protected $privateKey;

    /**
     * Commerce Public Certificate
     *
     * @var string
     */
    protected $publicCert;

    /**
     * SoapAdapter constructor.
     *
     * @param string $wsdl
     * @param string $privateKey
     * @param string $publicCert
     * @param array|null $options
     */
    public function __construct(string $wsdl, string $privateKey, string $publicCert, array $options = null)
    {
        $this->privateKey = $privateKey;
        $this->publicCert = $publicCert;

        parent::__construct($wsdl, $options);
    }

    /**
     * Makes a Request and validates it
     *
     * @param string $request
     * @param string $location
     * @param string $saction
     * @param int $version
     * @param null $one_way
     * @return string
     */
    public function __doRequest($request, $location, $saction, $version, $one_way = null)
    {
        $process = new Process($request);
        $process->sign($this->privateKey);
        $process->addIssuer($this->publicCert);
        $signedRequest = $process->getXML();

        $retVal = parent::__doRequest(
            $signedRequest,
            $location,
            $saction,
            $version,
            $one_way
        );

        $doc = new DOMDocument();
        $doc->loadXML($retVal);
        return $doc->saveXML();
    }

}