<?php

namespace Transbank\Wrapper\TransactionFactories;

use Transbank\Wrapper\Transactions\OnepayTransaction;

class OnepayTransactionFactory extends TransactionFactory
{

    /**
     * Returns an instance of a Transaction
     *
     * @param string $type
     * @param array $items
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|\Transbank\Wrapper\Transactions\Cart|OnepayTransaction
     * @throws \Exception
     */
    protected function makeTransaction(string $type, array $items = [])
    {
        return $this->prepareTransaction($type, new OnepayTransaction($items));
    }

    /**
     * Makes a OnepayTransaction, optionally with Items inside it
     *
     * @param array $items
     * @return \Transbank\Wrapper\Transactions\ServiceTransaction|\Transbank\Wrapper\Transactions\Cart|OnepayTransaction
     * @throws \Exception
     */
    public function makeCart(array $items = [])
    {
        return $this->makeTransaction('onepay.cart', $items);
    }

    /**
     * Creates a OnepayTransaction and immediately sends it to Transbank
     *
     * @param array $items
     * @return \Transbank\Wrapper\Results\ServiceResult
     * @throws \Exception
     */
    public function createCart(array $items)
    {
        return $this->makeCart($items)->getResult();
    }


}