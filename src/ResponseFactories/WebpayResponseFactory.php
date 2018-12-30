<?php

namespace DarkGhostHunter\TransbankApi\ResponseFactories;

class WebpayResponseFactory extends AbstractResponseFactory
{
    /*
    |--------------------------------------------------------------------------
    | Webpay Plus Normal
    |--------------------------------------------------------------------------
    */

    /**
     * Retrieves and Confirms a Webpay Plus Normal WebpayClient
     *
     * @param string $transaction
     * @return \DarkGhostHunter\TransbankApi\Contracts\ResponseInterface|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\RetrievingNoTransactionTypeException
     */
    public function getNormal(string $transaction)
    {
        return $this->service->getTransaction($transaction, 'plus.normal');
    }

    /**
     * Retrieves a Webpay Plus Normal WebpayClient
     *
     * @param string $transaction
     * @return \DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     */
    public function retrieveNormal(string $transaction)
    {
        return $this->service->retrieveTransaction($transaction, 'plus.normal');
    }

    /**
     * Confirms a Webpay Plus Normal WebpayClient
     *
     * @param string $transaction
     * @return bool
     */
    public function confirmNormal(string $transaction)
    {
        return $this->service->confirmTransaction($transaction, 'plus.normal');
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Plus Mall Normal
    |--------------------------------------------------------------------------
    */

    /**
     * Retrieves and Confirms a Webpay Plus Mall Normal WebpayClient
     *
     * @param string $transaction
     * @return \DarkGhostHunter\TransbankApi\Contracts\ResponseInterface|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\RetrievingNoTransactionTypeException
     */
    public function getMallNormal(string $transaction)
    {
        return $this->service->getTransaction($transaction, 'plus.mall.normal');
    }

    /**
     * Retrieves a Webpay Plus Mall Normal WebpayClient
     *
     * @param string $transaction
     * @return \DarkGhostHunter\TransbankApi\Contracts\ResponseInterface|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse
     */
    public function retrieveMallNormal(string $transaction)
    {
        return $this->service->retrieveTransaction($transaction, 'plus.mall.normal');
    }

    /**
     * Confirms a Webpay Plus Mall Normal WebpayClient
     *
     * @param string $transaction
     * @return bool
     */
    public function confirmMallNormal(string $transaction)
    {
        return $this->service->confirmTransaction($transaction, 'plus.mall.normal');
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Plus Deferred
    |--------------------------------------------------------------------------
    */

    /**
     * Retrieves and Confirms a Webpay Plus Deferred WebpayClient
     *
     * @param string $transaction
     * @return \DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Webpay\RetrievingNoTransactionTypeException
     */
    public function getDefer(string $transaction)
    {
        return $this->service->getTransaction($transaction, 'plus.defer');
    }

    /**
     * Retrieves a Webpay Plus Deferred WebpayClient
     *
     * @param string $transaction
     * @return \DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     */
    public function retrieveDefer(string $transaction)
    {
        return $this->service->retrieveTransaction($transaction, 'plus.defer');
    }

    /**
     * Confirms a Webpay Plus Deferred WebpayClient
     *
     * @param string $transaction
     * @return bool
     */
    public function confirmDefer(string $transaction)
    {
        return $this->service->confirmTransaction($transaction, 'plus.defer');
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Oneclick Registration
    |--------------------------------------------------------------------------
    */

    /**
     * Confirms the Webpay Oneclick Registration
     *
     * @param string $transaction
     * @return bool
     */
    public function getRegistration(string $transaction)
    {
        return $this->service->confirmTransaction($transaction, 'oneclick.confirm');
    }

    /**
     * Alias for getRegistration()
     *
     * @param string $transaction
     * @return bool
     */
    public function confirmRegistration(string $transaction)
    {
        return $this->getRegistration($transaction);
    }

}