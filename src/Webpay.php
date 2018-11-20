<?php

namespace Transbank\Wrapper;

use Exception;
use Transbank\Wrapper\Adapters\WebpayAdapter;
use Transbank\Wrapper\Exceptions\Credentials\CredentialInvalidException;
use Transbank\Wrapper\Helpers\Helpers;
use Transbank\Wrapper\Results\ServiceResult;
use Transbank\Wrapper\Results\WebpayMallResult;
use Transbank\Wrapper\Results\WebpayResult;
use Transbank\Wrapper\TransactionFactories\WebpayTransactionFactory;
use Transbank\Wrapper\Transactions\ServiceTransaction;

/**
 * Class Webpay
 * @package Transbank\Wrapper
 * 
 * @method Transactions\WebpayTransaction       makeNormal(array $attributes = [])
 * @method Results\WebpayResult                 createNormal(array $attributes)
 * @method Transactions\WebpayMallTransaction   makeMallNormal(array $attributes = [])
 * @method Results\WebpayMallResult             createMallNormal(array $attributes)
 * @method Transactions\WebpayTransaction       makeDefer(array $attributes = [])
 * @method Results\WebpayResult                 createDefer(array $attributes)
 * @method Transactions\WebpayMallTransaction   makeMallDefer(array $attributes = [])
 * @method Results\WebpayMallResult             createMallDefer(array $attributes)
 * @method Transactions\WebpayTransaction       makeCapture(array $attributes = [])
 * @method Results\WebpayResult                 createCapture(array $attributes)
 * @method Transactions\WebpayTransaction       makeMallCapture(array $attributes = [])
 * @method Results\WebpayResult                 createMallCapture(array $attributes)
 * @method Transactions\WebpayTransaction       makeNullify(array $attributes = [])
 * @method Results\WebpayResult                 createNullify(array $attributes)
 * @method Transactions\WebpayTransaction       makeRegistration(array $attributes = [])
 * @method Results\WebpayResult                 createRegistration(array $attributes)
 * @method Transactions\WebpayTransaction       makeUnregistration(array $attributes = [])
 * @method Results\WebpayResult                 createUnregistration(array $attributes)
 * @method Transactions\WebpayTransaction       makeCharge(array $attributes = [])
 * @method Results\WebpayResult                 createCharge(array $attributes)
 * @method Transactions\WebpayTransaction       makeReverseCharge(array $attributes = [])
 * @method Results\WebpayResult                 createReverseCharge(array $attributes)
 * @method Transactions\WebpayMallTransaction   makeMallCharge(array $attributes = [])
 * @method Results\WebpayMallResult             createMallCharge(array $attributes)
 * @method Transactions\WebpayMallTransaction   makeMallReverseCharge(array $attributes = [])
 * @method Results\WebpayMallResult             createMallReverseCharge(array $attributes)
 * @method Transactions\WebpayMallTransaction   makeMallNullify(array $attributes = [])
 * @method Results\WebpayMallResult             createMallNullify(array $attributes)
 * @method Transactions\WebpayMallTransaction   makeMallReverseNullify(array $attributes = [])
 * @method Results\WebpayMallResult             createMallReverseNullify(array $attributes)
 */
class Webpay extends AbstractService
{
    /**
     * Name of the default Webpay Public Certificate
     *
     * @const string
     */
    protected const WEBPAY_CERT = 'webpay.cert';

    /**
     * Class in charge of dispatching a Transaction
     *
     * @var WebpayAdapter
     */
    protected $adapter;

    /**
     * Transaction Factory to use for forwarding calls
     *
     * @example \Transbank\Wrapper\Webpay\TransactionFactory::class
     * @var string
     */
    protected $factory;

    /**
     * Boot any logic needed for the Service, like the Adapter and Factory;
     *
     * @return void
     */
    public function bootAdapter()
    {
        $this->adapter = new WebpayAdapter();
        $this->adapter->setIsProduction($this->isProduction());
    }

    /**
     * Instantiates (and/or boots) the Transaction Factory for the Service
     *
     * @return void
     */
    public function bootFactory()
    {
        $this->factory = new WebpayTransactionFactory($this, $this->defaults);
    }

    /**
     * Get the Service Credentials for the Environment
     *
     * @param ServiceTransaction $transaction
     * @return mixed
     */
    protected function getProductionCredentials(ServiceTransaction $transaction)
    {
        if ($cert = $this->getWebpayCredentialsForEnvironment()) {
            return ['webpayCert' => $cert];
        }
    }

    /**
     * Returns the Webpay Public Certificate depending on the environment
     *
     * @return bool|string
     */
    protected function getWebpayCredentialsForEnvironment()
    {
        return file_get_contents(
            $this->environmentCredentialsDirectory() . self::WEBPAY_CERT
        );
    }

    /**
     * Retrieve the Integration Credentials depending on the Transaction type
     *
     * @param ServiceTransaction $transaction
     * @return array
     * @throws Exception
     */
    protected function getIntegrationCredentials(ServiceTransaction $transaction)
    {
        // Get the directory path for the credentials for the transaction
        $environmentDir = $this->environmentCredentialsDirectory();

        $directory = $environmentDir . $this->credentialsForTransactionType($transaction->getType());

        // List the folder contents from the transaction $type
        $contents = Helpers::dirContents($directory);

        // Return the credentials or fail miserably
        $credentials = [
            'commerceCode' => $commerceCode = strtok($contents[0], '.'),
            'privateKey' => file_get_contents($directory . "$commerceCode.key"),
            'publicCert' => file_get_contents($directory . "$commerceCode.cert"),
            'webpayCert' => $this->getWebpayCredentialsForEnvironment(),
        ];

        if ($credentials['privateKey'] && $credentials['publicCert'] && $credentials['webpayCert']) {
            return $credentials;
        }

        throw new CredentialInvalidException(
            'Could not retrieve Integration Credentials. Ensure they are readable.'
        );
    }

    /**
     * Gets the directory of credentials for the transaction type
     *
     * @param string $type
     * @return string
     */
    protected function credentialsForTransactionType(string $type)
    {
        switch (true) {
            case strpos($type, 'mall') !== false:
                $directory = 'webpay-plus-mall';
                break;
            case strpos($type, 'capture') !== false:
                $directory = 'webpay-plus-capture';
                break;
            case strpos($type, 'oneclick') !== false:
                $directory = 'webpay-oneclick-normal';
                break;
            default:
                $directory = 'webpay-plus-normal';
                break;
        }

        return $directory . '/';
    }

    /**
     * Gets and Acknowledges a Transaction in Transbank
     *
     * @param $transaction
     * @return ServiceResult
     */
    public function confirmTransaction($transaction)
    {
        // TODO: Implement confirmTransaction() method.
    }

    /**
     * Gets a Transaction from Transbank
     *
     * @param string $token
     * @return ServiceResult
     */
    public function getTransaction(string $token)
    {
        $this->parseToTransactionResult(
            $this->adapter->get($token)
        );
    }

    /**
     * Acknowledges a Transaction in Transbank
     *
     * @param string $token
     * @return bool
     */
    public function acknowledgeTransaction(string $token)
    {
        // TODO: Implement acknowledgeTransaction() method.
    }

    /**
     * Confirms a Webpay Oneclick Registration
     *
     * @param string $token
     */
    public function confirmRegistration(string $token)
    {
        // TODO: Implement confirmRegistration() method.
    }

    /**
     * Transform the adapter raw Result to a Transaction Result
     *
     * @param $result
     * @return ServiceResult|WebpayResult|WebpayMallResult
     */
    protected function parseToTransactionResult($result)
    {
        $body = $result['body'] ?? [];

        return strpos($result['type'], 'mall') !== false
            ? new WebpayMallResult($body)
            : new WebpayResult($body);
    }
}