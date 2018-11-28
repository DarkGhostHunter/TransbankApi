<?php

namespace DarkGhostHunter\TransbankApi\Adapters;

use DarkGhostHunter\TransbankApi\Contracts\TransactionInterface;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\InvalidWebpayTransactionException;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\ServiceSdkUnavailableException;
use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use DarkGhostHunter\TransbankApi\Transactions\WebpayMallTransaction;
use DarkGhostHunter\TransbankApi\Clients\Webpay\WebpayCapture;
use DarkGhostHunter\TransbankApi\Clients\Webpay\WebpayNormal;
use DarkGhostHunter\TransbankApi\Clients\Webpay\WebpayNullify;
use DarkGhostHunter\TransbankApi\Clients\Webpay\WebpayOneclick;

class WebpaySoapAdapter extends AbstractAdapter
{

    /**
     * WebpaySoap Client holder
     *
     * @var \DarkGhostHunter\TransbankApi\Clients\Webpay\Transaction|WebpayNormal|WebpayCapture|WebpayNullify|WebpayOneclick
     */
    protected $client;

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
                $processor = WebpayNormal::class;
                break;
            case 'plus.capture':
            case 'plus.mall.capture':
                $processor = WebpayCapture::class;
                break;
            case 'plus.nullify':
            case 'plus.mall.nullify':
                $processor = WebpayNullify::class;
                break;
            case 'oneclick.register':
            case 'oneclick.confirm':
            case 'oneclick.unregister':
            case 'oneclick.charge':
            case 'oneclick.reverse':
                $processor = WebpayOneclick::class;
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

            // WebpaySoap doesn't throw exceptions, so we have to use our own logic to do so.
            // Even if, it's a bit troublesome to get what the actual error is but
            // we're doing our best shot to get something more standard.
            switch ($type = $transaction->getType()) {
                case 'plus.normal':
                case 'plus.defer':
                    return $this->client->commit(
                        $transaction->buyOrder,
                        $transaction->sessionId,
                        $transaction->returnUrl,
                        $transaction->finalUrl,
                        $transaction->amount
                    );
                case 'plus.mall.normal':
                case 'plus.mall.defer':
                    return $this->client->commit(
                        $transaction->buyOrder,
                        $transaction->sessionId,
                        $transaction->returnUrl,
                        $transaction->finalUrl,
                        $transaction->getItems()
                    );
                case 'plus.capture':
                case 'plus.mall.capture':
                    return $this->client->capture(
                        $transaction->authorizationCode,
                        $transaction->captureAmount,
                        $transaction->buyOrder,
                        $transaction->commerceCode
                    );
                case 'plus.nullify':
                case 'plus.mall.nullify':
                    return $this->client->nullify(
                        $transaction->authorizationCode,
                        $transaction->authorizedAmount,
                        $transaction->buyOrder,
                        $transaction->nullifyAmount,
                        $transaction->commerceCode ?? null
                    );
                case 'oneclick.register':
                    return $this->client->register(
                        $transaction->username,
                        $transaction->email,
                        $transaction->responseUrl
                    );
                case 'oneclick.confirm':
                    return $this->client->confirm(
                        $transaction->token
                    );
                case 'oneclick.unregister':
                    return $this->client->unregister(
                        $transaction->tbkUser,
                        $transaction->username
                    );
                case 'oneclick.charge':
                    return $this->client->charge(
                        $transaction->buyOrder,
                        $transaction->tbkUser,
                        $transaction->username,
                        $transaction->amount
                    );
                case 'oneclick.reverse':
                    return $this->client->reverse($transaction->buyOrder);
                case 'oneclick.mall.charge':
                case 'oneclick.mall.reverse':
                case 'oneclick.mall.nullify':
                case 'oneclick.mall.reverseNullify':
                default:
                    throw new ServiceSdkUnavailableException();
            }
        } catch (\Exception $exception) {
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
    public function get($transaction, $options = null)
    {
        $this->bootClient($options);

        switch ($options) {
            case 'plus.normal':
            case 'plus.mall.normal':
                return $this->client->get($transaction);
            case 'oneclick.register':
                return $this->client->getOneClickTransaction()
                    ->finishInscription($transaction);
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
    public function retrieve($transaction, $options = null)
    {
        $this->bootClient($options);

        switch ($options) {
            case 'plus.normal':
            case 'plus.mall.normal':
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
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException
     */
    public function confirm($transaction, $options = null)
    {
        $this->bootClient($options);

        switch ($options) {
            case 'plus.normal':
            case 'plus.mall.normal':
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