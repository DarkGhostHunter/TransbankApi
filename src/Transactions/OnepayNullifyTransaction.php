<?php

namespace DarkGhostHunter\TransbankApi\Transactions;

use Closure;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\CartEmptyException;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\CartNegativeAmountException;
use DarkGhostHunter\TransbankApi\Transactions\Concerns\HasItems;

/**
 * Class OnepayTransaction
 * @package DarkGhostHunter\TransbankApi\Transactions
 */
class OnepayNullifyTransaction extends AbstractTransaction
{

    /*
    |--------------------------------------------------------------------------
    | Logic
    |--------------------------------------------------------------------------
    */

    /**
     * Fill any empty attributes depending on the transaction type
     */
    protected function fillEmptyAttributes()
    {
        // Set the time this is being committed as a timestamp
        $this->issuedAt = time();
    }
}