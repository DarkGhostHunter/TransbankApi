<?php

namespace DarkGhostHunter\TransbankApi\Transactions;

use DarkGhostHunter\TransbankApi\Transactions\Concerns\HasItems;

/**
 * Class WebpayMallTransaction
 * @package DarkGhostHunter\TransbankApi\Transactions
 *
 * @method \DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse commit()
 * @method \DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse forceCommit()
 */
class WebpayMallTransaction extends WebpayTransaction
{
    use HasItems;

    /**
     * Item defaults
     *
     * @var string
     */
    protected $itemDefaults = [
        'sessionId' => null,
    ];

    /**
     * OnepayTransaction constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (isset($attributes['items'])) {
            $this->setItemsFromConstruct($attributes['items']);
            unset($attributes['items']);
        }

        parent::__construct($attributes);
    }

    /**
     * Dynamically call the Items helpers as *Order* methods
     *
     * @param string $method
     * @param array $parameters
     * @return WebpayTransaction
     */
    public function __call($method, $parameters)
    {
        if (strpos('Order', $method)) {
            return $this->{str_replace('Order', 'Item', $method)}($parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Transform the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            $this->attributes,
            ['items' => $this->items]
        );
    }

}