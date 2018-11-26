<?php

namespace Transbank\Wrapper\Transactions;

use Closure;
use Transbank\Wrapper\Exceptions\Onepay\CartEmptyException;
use Transbank\Wrapper\Exceptions\Onepay\CartNegativeAmountException;
use Transbank\Wrapper\Transactions\Concerns\HasItems;

/**
 * Class OnepayTransaction
 * @package Transbank\Wrapper\Transactions
 */
class OnepayTransaction extends AbstractServiceTransaction
{
    use HasItems;

    /**
     * External Unique Number Generator for this cart
     *
     * @var string|Closure
     */
    protected $eunGenerator;

    /**
     * Item defaults
     *
     * @var array
     */
    protected $itemDefaults = [
        'quantity' => 1,
        'amount' => 0,
        'expire' => 0,
        'additionalData' => null
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
     * Automatically generates an External Unique Number for the Item
     *
     * @return string
     * @throws \Exception
     */
    protected function autoGenerateEun()
    {
        return bin2hex(random_bytes(16));
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
            if ($this->eunGenerator instanceof Closure) {
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
        if ($this->getType() === 'onepay.cart') {
            // Throw an Exception if the OnepayTransaction is being set with no amount
            if (empty($this->items)) {
                throw new CartEmptyException($this);
            }

            if (($this->total = $this->getTotal()) < 1) {
                throw new CartNegativeAmountException($this);
            }
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
            return new Item(array_merge($this->itemDefaults,$item));
        }

        if ($item instanceof Item && $item->quantity > 0) {
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
        $attributes = null;
        if ($this->items) {
            $attributes = array_merge(
                array_merge(
                    $this->attributes,
                    ['total' => $this->getTotal()]
                ),
                ['items' => $this->items]
            );
        }

        return $attributes ?? $this->attributes;
    }
}