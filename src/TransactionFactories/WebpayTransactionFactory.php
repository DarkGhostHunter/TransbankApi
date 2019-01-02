<?php

namespace DarkGhostHunter\TransbankApi\TransactionFactories;

use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use DarkGhostHunter\TransbankApi\Transactions\WebpayMallTransaction;

class WebpayTransactionFactory extends AbstractTransactionFactory
{
    use WebpayConcerns\HasWebpayOneclickTransactions;

    /*
    |--------------------------------------------------------------------------
    | Transaction Bases
    |--------------------------------------------------------------------------
    */

    /**
     * Returns an instance of a Transaction
     *
     * @param string $type
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayTransaction
     */
    protected function makeTransaction(string $type, array $attributes = [])
    {
        return $this->prepareTransaction($type, new WebpayTransaction($attributes));
    }

    /**
     * Returns an instance of a Transaction Mall
     *
     * @param string $type
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayMallTransaction
     */
    protected function makeTransactionMall(string $type, array $attributes = [])
    {
        return $this->prepareTransaction($type, new WebpayMallTransaction($attributes));
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Plus Normal
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Plus Normal transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayTransaction
     */
    public function makeNormal(array $attributes = [])
    {
        return $this->makeTransaction('plus.normal', $attributes);
    }

    /**
     * Commits a new Webpay Plus Normal transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     */
    public function createNormal(array $attributes)
    {
        return $this->makeNormal($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Plus Mall Normal
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Plus Mall Normal transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayMallTransaction
     */
    public function makeMallNormal(array $attributes = [])
    {
        return $this->makeTransactionMall('plus.mall.normal', $attributes);
    }

    /**
     * Commits a new Webpay Plus Mall Normal transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse
     */
    public function createMallNormal(array $attributes)
    {
        return $this->makeMallNormal($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Plus Deferred
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Plus Deferred transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayTransaction
     */
    public function makeDefer(array $attributes = [])
    {
        return $this->makeTransaction('plus.defer', $attributes);
    }

    /**
     * Commits a new Webpay Plus Deferred transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     */
    public function createDefer(array $attributes)
    {
        return $this->makeDefer($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Plus Mall Deferred
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Plus Mall Deferred transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayTransaction
     */
    public function makeMallDefer(array $attributes = [])
    {
        return $this->makeTransactionMall('plus.mall.defer', $attributes);
    }

    /**
     * Commits a new Webpay Plus Mall Deferred transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     */
    public function createMallDefer(array $attributes)
    {
        return $this->makeMallDefer($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Plus Capture
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Plus Deferred transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayTransaction
     */
    public function makeCapture(array $attributes = [])
    {
        return $this->makeTransaction('plus.capture', $attributes);
    }

    /**
     * Commits a new Webpay Plus Deferred transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     */
    public function createCapture(array $attributes)
    {
        return $this->makeCapture($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Plus Mall Capture
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Plus Mall Deferred transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayMallTransaction
     */
    public function makeMallCapture(array $attributes = [])
    {
        return $this->makeTransactionMall('plus.mall.capture', $attributes);
    }

    /**
     * Commits a new Webpay Plus Mall Capture transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     */
    public function createMallCapture(array $attributes)
    {
        return $this->makeMallCapture($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Plus Nullify
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Plus Nullify transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayTransaction
     */
    public function makeNullify(array $attributes = [])
    {
        return $this->makeTransaction('plus.nullify', $attributes);
    }

    /**
     * Commits a new Webpay Plus Nullify transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     */
    public function createNullify(array $attributes)
    {
        return $this->makeNullify($attributes)->commit();
    }


}