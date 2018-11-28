<?php

namespace DarkGhostHunter\TransbankApi\OnepayHttp;

use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;

class HttpClient
{

    public function post($transaction)
    {

        $signature = $transaction->signCommit($transaction, $credentials = null);
        
    }

    protected function signCommit(OnepayTransaction $transaction, $credentials)
    {

        $data = mb_strlen($transaction->externalUniqueNumber) . $transaction->externalUniqueNumber
            . mb_strlen($transaction->total) . $transaction->total
            . mb_strlen($transaction->itemsQuantity) . $transaction->itemsQuantity
            . mb_strlen($transaction->issuedAt) . $transaction->issuedAt
            . mb_strlen($transaction->callbackUrl) . $transaction->callbackUrl;

        $crypted = hash_hmac('sha256', $data, $secret, true);

        return base64_encode($crypted);
    }
}