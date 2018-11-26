<?php

namespace Transbank\Wrapper\Adapters;

use Transbank\Onepay\Item;
use Transbank\Onepay\OnepayBase;
use Transbank\Onepay\Refund;
use Transbank\Onepay\ShoppingCart;
use Transbank\Onepay\Transaction;
use Transbank\Wrapper\Contracts\TransactionInterface;
use Transbank\Wrapper\Exceptions\Onepay\OnepaySdkException;
use Transbank\Wrapper\Exceptions\Transbank\TransbankSdkException;
use Transbank\Wrapper\Transactions\OnepayTransaction;

class OnepayAdapter extends AbstractAdapter
{
    /**
     * Set ups the Onepay SDK
     *
     * @param TransactionInterface|null $transaction
     * @throws \Exception
     */
    protected function setUpOnepaySdk(TransactionInterface $transaction = null)
    {
        // Set the Integration type
        OnepayBase::setCurrentIntegrationType($this->isProduction ? 'LIVE' : 'TEST');

        if ($transaction) {
            // Set the App Scheme if the transaction will use the APP channel
            OnepayBase::setAppScheme($transaction->appScheme);
            OnepayBase::setCallbackUrl($transaction->callbackUrl);
        }

        // Set the credentials
        OnepayBase::setApiKey($this->credentials['apiKey']);
        OnepayBase::setSharedSecret($this->credentials['secret']);
    }

    /**
     * Commits a transaction into the Transbank SDK
     *
     * @param TransactionInterface|\Transbank\Wrapper\Transactions\OnepayTransaction $transaction
     * @param mixed|null $options
     * @return array
     * @throws \Exception
     */
    public function commit(TransactionInterface $transaction, $options = null)
    {
        $this->setUpOnepaySdk($transaction);

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
     * @throws OnepaySdkException
     */
    protected function commitCart(OnepayTransaction $transaction)
    {
        $cart = new ShoppingCart;

        // Add each Item into the Shopping Cart
        foreach ($transaction->getItems() as $item) {
            $cart->add(
                new Item(
                    $item->description,
                    $item->quantity,
                    $item->amount,
                    $item->additionalData,
                    $item->expire
                )
            );
        }

        switch (strtolower($transaction->channel)) {
            case 'mobile':  $channel = 'MOBILE';    break;
            case 'app':     $channel = 'APP';       break;
            case 'web':
            default:        $channel = 'WEB';       break;
        }

        // Catch the Transbank SDK Exception, return it as part of the OnepaySdkException
        try {
            $result = Transaction::create(
                $cart,
                $channel,
                $transaction->externalUniqueNumber
            );
        } catch (\Exception $exception) {
            throw new OnepaySdkException($exception);
        }

        return [
            'occ'                   => $result->getOcc(),
            'ott'                   => $result->getOtt(),
            'externalUniqueNumber'  => $result->getExternalUniqueNumber(),
            'qrCodeAsBase64'        => $result->getQrCodeAsBase64(),
            'issuedAt'              => $result->getIssuedAt(),
            'signature'             => $result->getSignature(),
            'responseCode'          => $result->getResponseCode(),
            'description'           => $result->getDescription(),
            'amount'                => $transaction->getTotal()
        ];
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
        $this->setUpOnepaySdk();

        try {
            $result = Transaction::commit($transaction[0], $transaction[1]);
        } catch (\Exception $exception) {
            throw new OnepaySdkException($exception);
        }

        return [
            'amount'                =>  $result->getAmount(),
            'authorizationCode'     =>  $result->getAuthorizationCode(),
            'buyOrder'              =>  $result->getBuyOrder(),
            'installmentsNumber'    =>  $result->getInstallmentsNumber(),
            'installmentsAmount'    =>  $result->getInstallmentsAmount(),
            'issuedAt'              =>  $result->getIssuedAt(),
            'occ'                   =>  $result->getOcc(),
            'transactionDesc'       =>  $result->getTransactionDesc(),
            'responseCode'          =>  $result->getResponseCode(),
            'description'           =>  $result->getDescription(),
        ];

    }
}