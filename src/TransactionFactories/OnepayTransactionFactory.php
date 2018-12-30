<?php

namespace DarkGhostHunter\TransbankApi\TransactionFactories;

use DarkGhostHunter\TransbankApi\Transactions\OnepayNullifyTransaction;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;

class OnepayTransactionFactory extends AbstractTransactionFactory
{
    /**
     * Returns an instance of a WebpayClient
     *
     * @param string $type
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction
     * @return OnepayTransaction
     * @throws \Exception
     */
    protected function makeTransaction(string $type, array $attributes = [])
    {
        switch ($type) {
            case 'onepay.nullify':
                return $this->prepareTransaction($type, new OnepayNullifyTransaction($attributes));
            case 'onepay.cart':
            default:
                return $this->prepareTransaction($type, new OnepayTransaction($attributes));
        }
    }

    /**
     * Makes a Onepay WebpayClient, optionally with Items inside it
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction
     * @return OnepayTransaction
     * @throws \Exception
     */
    public function makeCart(array $attributes = [])
    {
        return $this->makeTransaction('onepay.cart', $attributes);
    }

    /**
     * Creates a Onepay WebpayClient and immediately sends it to Transbank
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse
     * @return OnepayTransaction
     * @throws \Exception
     */
    public function createCart(array $attributes)
    {
        return $this->makeCart($attributes)->commit();
    }

    /**
     * Creates a new Onepay Nullify transaction
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction&OnepayNullifyTransaction
     * @return OnepayTransaction
     * @throws \Exception
     */
    public function makeNullify(array $attributes)
    {
        return $this->makeTransaction('onepay.nullify', $attributes);
    }

    /**
     * Creates a Onepay Nullify transaction and immediately sends it to Transbank
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse
     * @return \DarkGhostHunter\TransbankApi\Responses\OnepayResponse
     * @throws \Exception
     */
    public function createNullify(array $attributes)
    {
        return $this->makeNullify($attributes)->commit();
    }

}