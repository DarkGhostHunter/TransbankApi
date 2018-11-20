<?php

namespace Transbank\Wrapper\Adapters;

use Transbank\Webpay\Configuration;
use Transbank\Webpay\Webpay;
use Transbank\Wrapper\Contracts\TransactionInterface;
use Transbank\Wrapper\Exceptions\Webpay\InvalidWebpayTransactionException;
use Transbank\Wrapper\Exceptions\Webpay\ServiceSdkUnavailableException;
use Transbank\Wrapper\Transactions\WebpayTransaction;
use Transbank\Wrapper\Transactions\WebpayMallTransaction;

class WebpayAdapter extends AbstractAdapter
{

    /**
     * Webpay SDK holder
     *
     * @var Webpay
     */
    protected $webpaySdk;

    /**
     * Prepares the Transbank SDK for Webpay
     */
    protected function prepareTransbankSdk()
    {
        if (!$this->webpaySdk) {
            $configuration = new Configuration();
            $configuration->setEnvironment(
                $this->isProduction ? 'PRODUCCION' : 'INTEGRACION'
            );

            $configuration->setCommerceCode($this->credentials['commerceCode']);
            $configuration->setPrivateKey($this->credentials['privateKey']);
            $configuration->setPublicCert($this->credentials['publicCert']);
            $configuration->setWebpayCert($this->credentials['webpayCert']);

            $this->webpaySdk = new Webpay($configuration);
        }
    }

    /**
     * Sets credentials to use against Transbank SDK
     *
     * @param array $credentials
     * @return mixed
     */
    public function setCredentials(array $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * Commits a transaction into the Transbank SDK
     *
     * @param TransactionInterface|WebpayTransaction|WebpayMallTransaction $transaction
     * @param array $options
     * @return mixed
     * @throws InvalidWebpayTransactionException
     * @throws ServiceSdkUnavailableException
     */
    public function commit(TransactionInterface $transaction, array $options = [])
    {
        $this->prepareTransbankSdk();

        $result = null;

        // Webpay doesn't throw exceptions, so we have to use our own logic to do so.
        // Even if, it's a bit troublesome to get what the actual error is but
        // we're doing our best shot to get something more standard.
        switch ($type = $transaction->getType()) {
            case 'plus.normal':
                $result = $this->webpaySdk->getNormalTransaction()
                    ->initTransaction(...[
                        $transaction->amount,
                        $transaction->buyOrder,
                        $transaction->sessionId,
                        $transaction->returnUrl,
                        $transaction->finalUrl
                    ]);
                break;
            case 'plus.mall.normal':
                $result = $this->webpaySdk->getMallNormalTransaction()
                    ->initTransaction(...[
                        $transaction->buyOrder,
                        $transaction->sessionId,
                        $transaction->returnUrl,
                        $transaction->finalUrl,
                        $transaction->getItems(),
                    ]);
                break;
            case 'plus.capture':
            case 'plus.mall.capture':
                $result = $this->webpaySdk->getCaptureTransaction()
                    ->capture(...array_merge([
                            $transaction->authorizationCode,
                            $transaction->captureAmount,
                            $transaction->buyOrder,
                        ], $transaction->commerceCode ?? [])
                    );
                break;
            case 'plus.nullify':
            case 'plus.mall.nullify':
                $result = $this->webpaySdk->getNullifyTransaction()
                    ->nullify(...[
                            $transaction->authorizationCode,
                            $transaction->authorizedAmount,
                            $transaction->buyOrder,
                            $transaction->nullifyAmount,
                            $transaction->commerceCode ?? null
                        ]
                    );
                break;
            case 'oneclick.register':
                $result = $this->webpaySdk->getOneClickTransaction()
                    ->initInscription(...[
                            $transaction->username,
                            $transaction->email,
                            $transaction->responseUrl,
                        ]
                    );
                break;
            case 'oneclick.unregister':
                $result = $this->webpaySdk->getOneClickTransaction()
                    ->removeUser(...[
                            $transaction->tbkUser,
                            $transaction->username,
                        ]
                    );
                break;
            case 'oneclick.charge':
                $result = $this->webpaySdk->getOneClickTransaction()
                    ->authorize(...[
                            $transaction->buyOrder,
                            $transaction->tbkUser,
                            $transaction->username,
                            $transaction->amount,
                        ]
                    );
                break;
            case 'oneclick.reverse':
                $result = $this->webpaySdk->getOneClickTransaction()
                    ->reverseTransaction($transaction->buyOrder);

                $result = $result->reversed ? $result : (array)$result;

                break;
            case 'oneclick.mall.charge':
            case 'oneclick.mall.reverse':
            case 'oneclick.mall.nullify':
            case 'oneclick.mall.reverseNullify':
                throw new ServiceSdkUnavailableException();
                break;
        }

        // Webpay always throws and object on success, an array on failure.
        // Oneclick will always return an object, though, but it will tell
        // with 'reversed' if it was success or not
        if (is_object($result)) {
            return [
                'type' => $type,
                'body' => (array)$result
            ];
        }

        $this->detectErrors($transaction, $result);
    }


    /**
     * Receives the result and throws an error (if it exists) or returns the result
     *
     * @param $transaction
     * @param $result
     * @throws InvalidWebpayTransactionException
     */
    protected function detectErrors($transaction, $result)
    {
        if (is_array($result) && array_key_exists('detail', $result)) {
            throw new InvalidWebpayTransactionException($result['detail'], $transaction);
        }

        if (is_object($result) && property_exists($result, 'reversed')) {
            throw new InvalidWebpayTransactionException(
                'Oneclick transaction was not reversed',
                $transaction
            );
        }

        throw new InvalidWebpayTransactionException(null, $transaction);

    }

    /**
     * Retrieves and Acknowledges a transaction into the Transbank SDK
     *
     * @param $transaction
     * @param array $options
     * @return mixed
     */
    public function confirm($transaction, array $options = [])
    {
        $this->prepareTransbankSdk();

        $processor = $this->webpaySdk->getNormalTransaction();

        return (array)$processor->getTransactionResult($transaction);
    }

    /**
     * Retrieves a transaction in the Transbank SDK
     *
     * @param $transaction
     * @param array $options
     * @return mixed
     */
    public function get($transaction, array $options = [])
    {
        // TODO: Implement get() method.
    }

    /**
     * Acknowledges a transaction in the Transbank SDK
     *
     * @param $transaction
     * @param array $options
     * @return mixed
     */
    public function acknowledge($transaction, array $options = [])
    {
        // TODO: Implement acknowledge() method.
    }

    /**
     * Return the Error Code from the Response
     *
     * @return mixed
     */
    public function getErrorCode() : string
    {
        // TODO: Implement getErrorCode() method.
    }

    /**
     * Translates the Error Code to a humanized string
     *
     * @return mixed
     */
    public function getErrorForHumans() : string
    {
        // TODO: Implement getErrorForHumans() method.
    }

}