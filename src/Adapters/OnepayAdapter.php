<?php

namespace DarkGhostHunter\TransbankApi\Adapters;

use DarkGhostHunter\TransbankApi\Clients\Onepay\OnepayClient;
use DarkGhostHunter\TransbankApi\Contracts\TransactionInterface;
use DarkGhostHunter\TransbankApi\Transactions\OnepayNullifyTransaction;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;

class OnepayAdapter extends AbstractAdapter
{
    /**
     * Onepay HTTP Client
     *
     * @var \DarkGhostHunter\TransbankApi\Clients\Onepay\OnepayClient
     */
    protected $client;

    /**
     * Set ups the Onepay SDK
     *
     * @throws \Exception
     */
    protected function bootClient()
    {
        $this->client = new OnepayClient($this->isProduction, $this->credentials);
    }

    /**
     * Commits a transaction into the Transbank SDK
     *
     * @param TransactionInterface|\DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction|\DarkGhostHunter\TransbankApi\Transactions\OnepayNullifyTransaction $transaction
     * @param mixed|null $options
     * @return array
     * @throws \Exception
     */
    public function commit(TransactionInterface $transaction, $options = null)
    {
        $this->bootClient();

        switch ($transaction->getType()) {
            case 'onepay.cart':
                return $this->commitCart($transaction);
            case 'onepay.nullify':
                return $this->commitNullify($transaction);
        }
    }

    /**
     * Commits a Onepay Cart
     *
     * @param OnepayTransaction $transaction
     * @return array
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepayValidationException
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepayResponseErrorException
     */
    protected function commitCart(OnepayTransaction $transaction)
    {
        return $this->client->commit($transaction);
    }

    /**
     * Commits a Nullify transaction
     *
     * @param OnepayNullifyTransaction $transaction
     * @return array
     * @throws \DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepayResponseErrorException
     */
    protected function commitNullify(OnepayNullifyTransaction $transaction)
    {
        return $this->client->refund($transaction);
    }

    /**
     * Retrieves and Acknowledges a transaction into the Transbank SDK
     *
     * @param $transaction
     * @param mixed|null $options
     * @return mixed
     * @throws \Exception
     */
    public function retrieveAndConfirm($transaction, $options = null)
    {
        $this->bootClient();

        return $this->client->confirm(new OnepayTransaction($transaction));
    }
}