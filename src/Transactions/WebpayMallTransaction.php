<?php

namespace Transbank\Wrapper\Transactions;

use Exception;
use Transbank\Wrapper\Transactions\Concerns\HasItems;
use Transbank\Wrapper\Webpay\MallItem;

/**
 * Class WebpayMallTransaction
 * @package Transbank\Wrapper\Transactions
 *
 * @method \Transbank\Wrapper\Results\WebpayMallResult getResult()
 * @method \Transbank\Wrapper\Results\WebpayMallResult forceGetResult()
 */
class WebpayMallTransaction extends WebpayTransaction
{
    use HasItems;

    /**
     * Item Class to instantiate
     *
     * @var string
     */
    protected $itemClass = MallItem::class;

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
     * Dynamically call the Items helpers as *Order methods
     *
     * @param string $method
     * @param array $parameters
     * @return WebpayTransaction
     */
    public function __call($method, $parameters)
    {
        if (strpos('Order', $method)) {
            return $this->{str_replace('Order','Item',$method)}($parameters);
        }

        return parent::__call($method, $parameters);
    }

    public function toArray()
    {
        return array_merge(
            $this->attributes,
            ['items' => $this->items]
        );
    }

}