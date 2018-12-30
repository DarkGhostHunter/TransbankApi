<?php

namespace DarkGhostHunter\TransbankApi\Adapters;

use DarkGhostHunter\TransbankApi\Contracts\TransactionInterface;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\InvalidWebpayTransactionException;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\ServiceSdkUnavailableException;
use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use DarkGhostHunter\TransbankApi\Transactions\WebpayMallTransaction;
use DarkGhostHunter\TransbankApi\Clients\Webpay\PlusCapture;
use DarkGhostHunter\TransbankApi\Clients\Webpay\PlusNormal;
use DarkGhostHunter\TransbankApi\Clients\Webpay\PlusNullify;
use DarkGhostHunter\TransbankApi\Clients\Webpay\OneclickNormal;
use Exception;

class WebpayAdapter extends AbstractAdapter
{
    /**
     * Boots the WebpaySoap Processor for the transaction type
     *
     * @param string $type
     * @throws ServiceSdkUnavailableException
     */
    protected function bootClient(string $type)
    {
        // We could have used an array and check for every key, but since some
        // transactions share the same logic (class), a switch it's better
        // suited for this.
        switch ($type) {
            case 'plus.normal':
            case 'plus.defer':
            case 'plus.mall.normal':
            case 'plus.mall.defer':
                $processor = PlusNormal::class;
                break;
            case 'plus.capture':
            case 'plus.mall.capture':
                $processor = PlusCapture::class;
                break;
            case 'plus.nullify':
            case 'plus.mall.nullify':
                $processor = PlusNullify::class;
                break;
            case 'oneclick.register':
            case 'oneclick.confirm':
            case 'oneclick.unregister':
            case 'oneclick.charge':
            case 'oneclick.reverse':
                $processor = OneclickNormal::class;
                break;
            case 'oneclick.mall.charge':
            case 'oneclick.mall.reverse':
            case 'oneclick.mall.nullify':
            case 'oneclick.mall.reverseNullify':
            default:
                throw new ServiceSdkUnavailableException();
        }

        $this->client = new $processor($this->isProduction, $this->credentials);
    }


    /**
     * Commits a transaction into the Transbank SDK
     *
     * @param TransactionInterface|WebpayTransaction|WebpayMallTransaction $transaction
     * @param mixed|null $options
     * @return mixed
     * @throws ServiceSdkUnavailableException
     * @throws InvalidWebpayTransactionException
     */
    public function commit(TransactionInterface $transaction, $options = null)
    {
        $this->bootClient($transaction->getType());

        try {
            switch ($type = $transaction->getType()) {
                case 'plus.normal':
                case 'plus.defer':
                case 'plus.mall.normal':
                case 'plus.mall.defer':
                    return $this->client->commit($transaction);
                case 'plus.capture':
                case 'plus.mall.capture':
                    return $this->client->capture($transaction);
                case 'plus.nullify':
                case 'plus.mall.nullify':
                    return $this->client->nullify($transaction);
                case 'oneclick.register':
                    return $this->client->register($transaction);
                case 'oneclick.confirm':
                    return $this->client->confirm($transaction);
                case 'oneclick.unregister':
                    return $this->client->unregister($transaction);
                case 'oneclick.charge':
                    return $this->client->charge($transaction);
                case 'oneclick.reverse':
                    return $this->client->reverse($transaction);
                case 'patpass.subscribe':
                case 'patpass.unsubscribe':
                case 'patpass.extend':
                case 'patpass.renew':
                case 'oneclick.mall.charge':
                case 'oneclick.mall.reverse':
                case 'oneclick.mall.nullify':
                case 'oneclick.mall.reverseNullify':
                default:
                    throw new ServiceSdkUnavailableException();
            }
        } catch (Exception $exception) {
            throw new InvalidWebpayTransactionException($transaction, $exception);
        }
    }

    /**
     * Retrieves and Confirms a transaction into the Transbank SDK
     *
     * @param $transaction
     * @param mixed|null $options
     * @return mixed
     * @throws ServiceSdkUnavailableException
     */
    public function getAndConfirm($transaction, $options = null)
    {
        $this->bootClient($options);

        switch ($options) {
            case 'plus.normal':
            case 'plus.mall.normal':
                return $this->client->retrieve($transaction);
            case 'oneclick.register':
                return $this->client->register($transaction);
            default:
                throw new ServiceSdkUnavailableException();
        }
    }


    /**
     * Retrieves a transaction from Transbank
     *
     * @param $transaction
     * @param string|array|null $options
     * @return mixed
     * @throws ServiceSdkUnavailableException
     */
    public function get($transaction, $options = null)
    {
        $this->bootClient($options);

        switch ($options) {
            case 'plus.normal':
            case 'plus.mall.normal':
            case 'patpass.subscribe':
                return $this->client->retrieve($transaction);
                break;
            default:
                throw new ServiceSdkUnavailableException();
        }
    }


    /**
     * Acknowledges a transaction in the Transbank SDK
     *
     * @param $transaction
     * @param string $options
     * @return mixed
     * @throws ServiceSdkUnavailableException
     */
    public function confirm($transaction, $options = null)
    {
        $this->bootClient($options);

        switch ($options) {
            case 'plus.normal':
            case 'plus.mall.normal':
            case 'patpass.subscribe':
                return $this->client->confirm($transaction);
                break;
            case 'oneclick.confirm':
                return $this->client->confirm($transaction);
                break;
            default:
                throw new ServiceSdkUnavailableException();
        }
    }

}