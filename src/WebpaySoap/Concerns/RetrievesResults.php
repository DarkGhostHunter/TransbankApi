<?php

namespace Transbank\Wrapper\WebpaySoap\Concerns;

use Transbank\Wrapper\Helpers\Fluent;

/**
 * Class PerformsGetTransactionResults
 *
 * @package Transbank\Wrapper\WebpaySoap\Concerns
 *
 * @mixin \Transbank\Wrapper\WebpaySoap\Transaction
 */
trait RetrievesResults
{
    /**
     * Returns the Transaction results
     *
     * @param $transaction
     * @return array
     */
    public function retrieve($transaction)
    {
        return (array)($this->connector->getTransactionResult(
            new Fluent([
                'tokenInput' => $transaction
            ])
        ))->return;
    }
}
