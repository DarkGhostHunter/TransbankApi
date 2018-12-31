<?php

namespace DarkGhostHunter\TransbankApi\Transactions;

use Closure;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\CartEmptyException;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\CartNegativeAmountException;
use DarkGhostHunter\TransbankApi\Helpers\Helpers;
use DarkGhostHunter\TransbankApi\Transactions\Concerns\HasItems;

/**
 * Class OnepayTransaction
 * @package DarkGhostHunter\TransbankApi\Transactions
 */
class OnepayNullifyTransaction extends AbstractTransaction
{
    use Concerns\HasSecrets;

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
        $this->issuedAt = $this->issuedAt ?? time();
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Array representation
    |--------------------------------------------------------------------------
    */

    /**
     * Transform the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        if ($this->hideSecrets) {
            return Helpers::arrayExcept($this->attributes, ['appKey', 'apiKey', 'signature']);
        }

        return $this->attributes;
    }
}