<?php

namespace DarkGhostHunter\TransbankApi\Transactions;

use DarkGhostHunter\TransbankApi\Transactions\Concerns\HasItems;

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
        if (strpos($method, 'Order') !== false) {
            return $this->{str_replace('Order', 'Item', $method)}(...$parameters);
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