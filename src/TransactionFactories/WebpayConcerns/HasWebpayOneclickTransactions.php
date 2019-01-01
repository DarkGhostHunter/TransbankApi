<?php

namespace DarkGhostHunter\TransbankApi\TransactionFactories\WebpayConcerns;

trait HasWebpayOneclickTransactions
{
    /*
    |--------------------------------------------------------------------------
    | Webpay Oneclick Registration
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Oneclick Registration transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayTransaction
     */
    public function makeRegistration(array $attributes = [])
    {
        return $this->makeTransaction('oneclick.register', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Registration transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     */
    public function createRegistration(array $attributes)
    {
        return $this->makeRegistration($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Oneclick Unregistration
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Oneclick Unregistration transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayTransaction
     */
    public function makeUnregistration(array $attributes = [])
    {
        return $this->makeTransaction('oneclick.unregister', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Unregistration transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     */
    public function createUnregistration(array $attributes)
    {
        return $this->makeUnregistration($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Oneclick Charge
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Oneclick Charge transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayTransaction
     */
    public function makeCharge(array $attributes = [])
    {
        return $this->makeTransaction('oneclick.charge', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Charge transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     */
    public function createCharge(array $attributes)
    {
        return $this->makeCharge($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Oneclick Reverse Charge
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Oneclick Reverse Charge transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayTransaction
     */
    public function makeReverseCharge(array $attributes = [])
    {
        return $this->makeTransaction('oneclick.reverse', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Reverse Charge transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse
     */
    public function createReverseCharge(array $attributes)
    {
        return $this->makeReverseCharge($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Oneclick Mall Charge
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Oneclick Mall Charge transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayMallTransaction
     */
    public function makeMallCharge(array $attributes = [])
    {
        return $this->makeTransactionMall('oneclick.mall.charge', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Mall Charge transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse
     */
    public function createMallCharge(array $attributes = [])
    {
        return $this->makeMallCharge($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Oneclick Reverse Charge
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Oneclick Mall Reverse Charge transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayMallTransaction
     */
    public function makeMallReverseCharge(array $attributes = [])
    {
        return $this->makeTransactionMall('oneclick.mall.reverse', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Mall Reverse Charge transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse
     */
    public function createMallReverseCharge(array $attributes = [])
    {
        return $this->makeMallReverseCharge($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Oneclick Mall Nullify
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Oneclick Mall Nullify transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayMallTransaction
     */
    public function makeMallNullify(array $attributes = [])
    {
        return $this->makeTransactionMall('oneclick.mall.nullify', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Mall Nullify transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse
     */
    public function createMallNullify(array $attributes = [])
    {
        return $this->makeMallNullify($attributes)->commit();
    }

    /*
    |--------------------------------------------------------------------------
    | Webpay Oneclick Mall Reverse Nullify
    |--------------------------------------------------------------------------
    */

    /**
     * Makes a new Webpay Oneclick Mall Reverse Nullify transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction|WebpayMallTransaction
     */
    public function makeMallReverseNullify(array $attributes = [])
    {
        return $this->makeTransactionMall('oneclick.mall.reverseNullify', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Mall Reverse Nullify transaction and returns the results
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse|\DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse
     */
    public function createMallReverseNullify(array $attributes = [])
    {
        return $this->makeMallReverseNullify($attributes)->commit();
    }
}