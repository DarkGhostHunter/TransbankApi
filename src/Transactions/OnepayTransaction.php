<?php

namespace Transbank\Wrapper\Transactions;

use Closure;
use Exception;
use Transbank\Wrapper\Exceptions\Onepay\CartEmptyException;
use Transbank\Wrapper\Exceptions\Onepay\CartNegativeAmountException;
use Transbank\Wrapper\Onepay\Item;
use Transbank\Wrapper\Transactions\Concerns\HasItems;

/**
 * Class OnepayTransaction
 * @package Transbank\Wrapper\Transactions
 *
 * @method \Transbank\Wrapper\Results\OnepayResult getResult()
 * @method \Transbank\Wrapper\Results\OnepayResult forceGetResult()
 *
 * @property
 */
class OnepayTransaction extends ServiceTransaction
{
    use HasItems;

    /**
     * External Unique Number Generator for this cart
     *
     * @var string|Closure
     */
    protected $eunGenerator;

    /**
     * Item Class to instantiate
     *
     * @var string
     */
    protected $itemClass = Item::class;

    /**
     * OnepayTransaction constructor.
     *
     * @param array $attributes
     * @throws Exception
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
     * Automatically generates an External Unique Number for the Item
     *
     * @return string
     */
    protected function autoGenerateEun()
    {
        return uniqid('', true);
    }

    /**
     * Sets logic to make an External Unique Number for the OnepayTransaction
     *
     * @param Closure $function
     */
    public function generateEun(Closure $function)
    {
        $this->eunGenerator = $function;
    }


    /**
     * Fill any empty attributes depending on the transaction type
     */
    protected function fillEmptyAttributes()
    {
        // If there is some kind of generator for the unique code, use it,
        // unless its already set by the developer.
        if (!$this->externalUniqueNumber) {
            if ($this->eunGenerator) {
                $closure = $this->eunGenerator;
                $this->externalUniqueNumber = $closure($this);
            } else {
                $this->externalUniqueNumber = $this->autoGenerateEun();
            }
        }

        // Add the Total
        $this->total = $this->getTotal();
    }


    /**
     * Does any logic before committing the transaction to a Result
     *
     * @throws CartNegativeAmountException|CartEmptyException
     */
    protected function performPreLogic()
    {
        // Throw an Exception if the OnepayTransaction is being set with no amount
        if (empty($this->items)) {
            throw new CartEmptyException($this);
        }

        if (($this->total = $this->getTotal()) < 1) {
            throw new CartNegativeAmountException($this);
        }

    }

    /**
     * Parse the Item we are getting
     *
     * @param $item
     * @return null
     */
    protected function parseItem($item)
    {
        if(is_string($item) && $array = json_decode($item, true)) {
            $item = $array;
        }

        if (is_array($item) && $item['quantity'] > 0) {
            return new $this->itemClass($item);
        }

        if ($item instanceof $this->itemClass && $item->quantity > 0) {
            return $item;
        }

        return null;
    }

    /**
     * Count all the Items by their quantity
     *
     * @return int
     */
    public function countItemsQuantity()
    {
        $quantity = 0;
        foreach ($this->items as $item) {
            $quantity += $item->quantity;
        }

        return $quantity;
    }

    /**
     * Get the total amount for all the Items in the cart
     *
     * @return int
     */
    public function getTotal()
    {
        $amount = 0;
        foreach ($this->items as $item) {
            $amount += (int)$item->amount;
        }
        return $amount;
    }

    /**
     * Transform the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            array_merge(
                $this->attributes,
                ['total' => $this->getTotal()]
            ),
            ['items' => $this->items]
        );
    }
}