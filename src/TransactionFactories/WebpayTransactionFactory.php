<?php

namespace Transbank\Wrapper\TransactionFactories;

use Transbank\Wrapper\Transactions\WebpayTransaction;
use Transbank\Wrapper\Transactions\WebpayMallTransaction;

class WebpayTransactionFactory extends TransactionFactory
{
    /**
     * Returns an instance of a Transaction
     *
     * @param string $type
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayTransaction
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
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayMallTransaction
     */
    protected function makeTransactionMall(string $type, array $attributes = [])
    {
        return $this->prepareTransaction($type, new WebpayMallTransaction($attributes));
    }

    /**
     * Makes a new Webpay Plus Normal transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayTransaction
     */
    public function makeNormal(array $attributes = [])
    {
        return $this->makeTransaction('plus.normal', $attributes);
    }

    /**
     * Commits a new Webpay Plus Normal transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayResult
     */
    public function createNormal(array $attributes)
    {
        return $this->makeNormal($attributes)->getResult();
    }

    /**
     * Makes a new Webpay Plus Mall Normal transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayMallTransaction
     */
    public function makeMallNormal(array $attributes = [])
    {
        return $this->makeTransactionMall('plus.mall.normal', $attributes);
    }

    /**
     * Commits a new Webpay Plus Mall Normal transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayMallResult
     */
    public function createMallNormal(array $attributes)
    {
        return $this->makeMallNormal($attributes)->getResult();
    }

    /**
     * Makes a new Webpay Plus Deferred transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayTransaction
     */
    public function makeCapture(array $attributes = [])
    {
        return $this->makeTransaction('plus.capture', $attributes);
    }

    /**
     * Commits a new Webpay Plus Deferred transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayResult
     */
    public function createCapture(array $attributes)
    {
        return $this->makeCapture($attributes)->getResult();
    }

    /**
     * Makes a new Webpay Plus Mall Deferred transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayTransaction
     */
    public function makeMallCapture(array $attributes = [])
    {
        return $this->makeTransaction('plus.mall.capture', $attributes);
    }

    /**
     * Commits a new Webpay Plus Mall Deferred transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayResult
     */
    public function createMallCapture(array $attributes)
    {
        return $this->makeMallCapture($attributes)->getResult();
    }

    /**
     * Makes a new Webpay Plus Nullify transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayTransaction
     */
    public function makeNullify(array $attributes = [])
    {
        return $this->makeTransaction('plus.nullify', $attributes);
    }

    /**
     * Commits a new Webpay Plus Nullify transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayResult
     */
    public function createNullify(array $attributes)
    {
        return $this->makeNullify($attributes)->getResult();
    }

    /**
     * Makes a new Webpay Oneclick Registration transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayTransaction
     */
    public function makeRegistration(array $attributes = [])
    {
        return $this->makeTransaction('oneclick.register', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Registration transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayResult
     */
    public function createRegistration(array $attributes)
    {
        return $this->makeRegistration($attributes)->getResult();
    }

    /**
     * Makes a new Webpay Oneclick Unregistration transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayTransaction
     */
    public function makeUnregistration(array $attributes = [])
    {
        return $this->makeTransaction('oneclick.unregister', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Unregistration transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayResult
     */
    public function createUnregistration(array $attributes)
    {
        return $this->makeUnregistration($attributes)->getResult();
    }

    /**
     * Makes a new Webpay Oneclick Charge transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayTransaction
     */
    public function makeCharge(array $attributes = [])
    {
        return $this->makeTransaction('oneclick.charge', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Charge transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayResult
     */
    public function createCharge(array $attributes)
    {
        return $this->makeCharge($attributes)->getResult();
    }

    /**
     * Makes a new Webpay Oneclick Reverse Charge transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayTransaction
     */
    public function makeReverseCharge(array $attributes = [])
    {
        return $this->makeTransaction('oneclick.reverse', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Reverse Charge transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayResult
     */
    public function createReverseCharge(array $attributes)
    {
        return $this->makeReverseCharge($attributes)->getResult();
    }

    /**
     * Makes a new Webpay Oneclick Mall Charge transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayMallTransaction
     */
    public function makeMallCharge(array $attributes = [])
    {
        return $this->makeTransactionMall('oneclick.mall.charge', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Mall Charge transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayMallResult
     */
    public function createMallCharge(array $attributes = [])
    {
        return $this->makeMallCharge($attributes)->getResult();
    }

    /**
     * Makes a new Webpay Oneclick Mall Reverse Charge transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayMallTransaction
     */
    public function makeMallReverseCharge(array $attributes = [])
    {
        return $this->makeTransactionMall('oneclick.mall.reverse', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Mall Reverse Charge transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayMallResult
     */
    public function createMallReverseCharge(array $attributes = [])
    {
        return $this->makeMallReverseCharge($attributes)->getResult();
    }

    /**
     * Makes a new Webpay Oneclick Mall Nullify transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayTransaction
     */
    public function makeMallNullify(array $attributes = [])
    {
        return $this->makeTransaction('oneclick.mall.nullify', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Mall Nullify transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayMallResult
     */
    public function createMallNullify(array $attributes = [])
    {
        return $this->makeMallNullify($attributes)->getResult();
    }

    /**
     * Makes a new Webpay Oneclick Mall Reverse Nullify transaction
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|WebpayTransaction
     */
    public function makeMallReverseNullify(array $attributes = [])
    {
        return $this->makeTransaction('oneclick.mall.reverseNullify', $attributes);
    }

    /**
     * Commits a new Webpay Oneclick Mall Reverse Nullify transaction and returns the results
     *
     * @param array $attributes
     * @return \Transbank\Wrapper\Results\ServiceResult|\Transbank\Wrapper\Results\WebpayMallResult
     */
    public function createMallReverseNullify(array $attributes = [])
    {
        return $this->makeMallReverseNullify($attributes)->getResult();
    }

}