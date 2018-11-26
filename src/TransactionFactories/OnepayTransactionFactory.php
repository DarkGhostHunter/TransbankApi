<?php

namespace DarkGhostHunter\TransbankApi\TransactionFactories;

use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;

class OnepayTransactionFactory extends AbstractTransactionFactory
{
    /**
     * Returns an instance of a Transaction
     *
     * @param string $type
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Transactions\AbstractServiceTransaction|OnepayTransaction
     * @throws \Exception
     */
    protected function makeTransaction(string $type, array $attributes = [])
    {
        return $this->prepareTransaction($type, new OnepayTransaction($attributes));
    }

    /**
     * Makes a Onepay Transaction, optionally with Items inside it
     *
     * @param array $attributes
     * @return OnepayTransaction
     * @throws \Exception
     */
    public function makeCart(array $attributes = [])
    {
        return $this->makeTransaction('onepay.cart', $attributes);
    }

    /**
     * Creates a Onepay Transaction and immediately sends it to Transbank
     *
     * @param array $attributes
     * @return \DarkGhostHunter\TransbankApi\Responses\OnepayResponse
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
     * @return \DarkGhostHunter\TransbankApi\Responses\OnepayResponse
     * @throws \Exception
     */
    public function createNullify(array $attributes)
    {
        return $this->makeNullify($attributes)->commit();
    }

}