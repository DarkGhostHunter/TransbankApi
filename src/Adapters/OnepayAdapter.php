<?php

namespace Transbank\Wrapper\Adapters;

use Transbank\Onepay\Item;
use Transbank\Onepay\OnepayBase;
use Transbank\Onepay\ShoppingCart;
use Transbank\Onepay\Transaction;
use Transbank\Wrapper\Contracts\TransactionInterface;
use Transbank\Wrapper\Exceptions\Transbank\TransbankSdkException;

class OnepayAdapter extends AbstractAdapter
{
    /**
     * Set ups the Onepay SDK
     *
     * @param TransactionInterface $transaction
     * @throws \Exception
     */
    protected function setUpOnepaySdk(TransactionInterface $transaction)
    {
        // Set the Integration type
        OnepayBase::setCurrentIntegrationType($this->isProduction ? 'LIVE' : 'TEST');

        // Set the App Scheme if the transaction will use the APP channel
        OnepayBase::setAppScheme($transaction->appScheme);
        OnepayBase::setCallbackUrl($transaction->callbackUrl);

        // Set the credentials
        OnepayBase::setApiKey($this->credentials['apiKey']);
        OnepayBase::setSharedSecret($this->credentials['secret']);
    }

    /**
     * Commits a transaction into the Transbank SDK
     *
     * @param TransactionInterface|\Transbank\Wrapper\Transactions\OnepayTransaction $transaction
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function commit(TransactionInterface $transaction, array $options = [])
    {
        $this->setUpOnepaySdk($transaction);

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

        $channel = 'WEB';

        switch (strtolower($transaction->channel)) {
            case 'mobile':  $channel = 'MOBILE';    break;
            case 'web':     $channel = 'WEB';       break;
            case 'app':     $channel = 'APP';       break;
        }

        // Catch the Transbank SDK Exception
        try {
            $result = Transaction::create(
                $cart,
                $channel,
                $transaction->externalUniqueNumber
            );
        } catch (\Exception $exception) {
            throw new TransbankSdkException($exception);
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
     * Retrieves and Acknowledges a transaction into the Transbank SDK
     *
     * @param $transaction
     * @param array $options
     * @return mixed
     */
    public function confirm($transaction, array $options = [])
    {
        // TODO: Implement confirm() method.
    }

    /**
     * Return the Error Code from the Response
     *
     * @return mixed
     */
    public function getErrorCode() : string
    {
        // TODO: Implement getErrorCode() method.
    }

    /**
     * Translates the Error Code to a humanized string
     *
     * @return mixed
     */
    public function getErrorForHumans() : string
    {
        // TODO: Implement getErrorForHumans() method.
    }
}