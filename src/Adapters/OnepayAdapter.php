<?php

namespace DarkGhostHunter\TransbankApi\Adapters;

use DarkGhostHunter\TransbankApi\Clients\Onepay\OnepayHttp;
use Transbank\Onepay\Item;
use Transbank\Onepay\Refund;
use Transbank\Onepay\ShoppingCart;
use Transbank\Onepay\Transaction;
use DarkGhostHunter\TransbankApi\Contracts\TransactionInterface;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepaySdkException;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;

class OnepayAdapter extends AbstractAdapter
{
    /**
     * Onepay HTTP Client
     *
     * @var \DarkGhostHunter\TransbankApi\Clients\Onepay\OnepayHttp
     */
    protected $client;

    /**
     * Set ups the Onepay SDK
     *
     * @throws \Exception
     */
    protected function bootClient()
    {
        $this->client = new OnepayHttp($this->isProduction, $this->credentials);
    }

    /**
     * Commits a transaction into the Transbank SDK
     *
     * @param TransactionInterface|\DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction $transaction
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
     * @param OnepayTransaction $transaction
     * @return array
     * @throws OnepaySdkException
     */
    protected function commitNullify(OnepayTransaction $transaction)
    {
        // Catch the Transbank SDK Exception, return it as part of the OnepaySdkException
        try {
            $result = Refund::create(
                $transaction->amount,
                $transaction->occ,
                $transaction->externalUniqueNumber,
                $transaction->authorizationCode
            );
        } catch (\Exception $exception) {
            throw new OnepaySdkException($exception);
        }

        return [
            'occ'                   => $result->getOcc(),
            'externalUniqueNumber'  => $result->getExternalUniqueNumber(),
            'reverseCode'           => $result->getReverseCode(),
            'issuedAt'              => $result->getIssuedAt(),
        ];
    }

    /**
     * Retrieves and Acknowledges a transaction into the Transbank SDK
     *
     * @param $transaction
     * @param mixed|null $options
     * @return mixed
     * @throws \Exception
     */
    public function get($transaction, $options = null)
    {
        $this->bootClient();

        return $this->client->confirm(new OnepayTransaction($transaction));
    }
}