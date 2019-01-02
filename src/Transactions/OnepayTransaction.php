<?php

namespace DarkGhostHunter\TransbankApi\Transactions;

use Closure;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\CartEmptyException;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\CartNegativeAmountException;
use DarkGhostHunter\TransbankApi\Helpers\Helpers;

/**
 * Class OnepayTransaction
 * @package DarkGhostHunter\TransbankApi\Transactions
 *
 * @property-read int $itemsQuantity
 * @property-read int $total
 *
 * @property string $externalUniqueNumber
 */
class OnepayTransaction extends AbstractTransaction
{
    use Concerns\HasItems,
        Concerns\HasSecrets;

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

    /*
    |--------------------------------------------------------------------------
    | Construct
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Getters and Setters
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | External Unique Number generation
    |--------------------------------------------------------------------------
    */

    /**
     * Sets logic to make an External Unique Number for the OnepayTransaction
     *
     * @param callable $function
     */
    public function generateEun(callable $function)
    {
        $this->eunGenerator = $function;
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

    /*
    |--------------------------------------------------------------------------
    | Logic
    |--------------------------------------------------------------------------
    */

    /**
     * Fill any empty attributes depending on the transaction type
     *
     * @return void
     * @throws \Exception
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
    }


    /**
     * Does any logic before committing the transaction to a Result
     *
     * @throws CartNegativeAmountException
     * @throws CartEmptyException
     */
    protected function performPreLogic()
    {
        // Throw an Exception if the OnepayTransaction is being set with no amount
        if (empty($this->items)) {
            throw new CartEmptyException($this);
        }

        if ($this->total < 1) {
            throw new CartNegativeAmountException($this);
        }

        // Set the time this is being committed as a timestamp
        $this->issuedAt = time();

        // Set the items quantity
        $this->itemsQuantity = $this->getItemsQuantityAttribute();

        // Uppercase the channel
        $this->channel = strtoupper($this->channel);
    }

    /*
    |--------------------------------------------------------------------------
    | Has Items Override
    |--------------------------------------------------------------------------
    */

    /**
     * Parse the Item we are getting
     *
     * @param $item
     * @return null
     */
    protected function parseItem($item)
    {
        // First, let's try to decode this Item
        if (is_string($item) && $array = json_decode($item, true)) {
            $item = $array;
        }

        // If it was decoded, then return the Item
        if (is_array($item) && ($item['quantity'] ?? 0) >= 1) {
            return new Item(array_merge($this->itemDefaults, $item));
        }

        return null;
    }

    /**
     * Updates an Item attributes, replacing or adding them, and return the result
     *
     * @param int $key
     * @param array $attributes
     * @return object|bool
     */
    public function updateItem(int $key, array $attributes)
    {
        if (isset($this->items[$key])) {

            $this->items[$key]->setAttributes(
                array_merge($this->items[$key]->getAttributes(), $attributes)
            );

            // Delete the updated item when its quantity is zero
            if ($this->items[$key]->quantity < 1) {
                $this->deleteItem($key);
                return false;
            }

            return $this->items[$key];
        }
        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Items attribute helpers functions
    |--------------------------------------------------------------------------
    */

    /**
     * Returns how many Items this Transaction has
     *
     * @return int
     */
    public function getItemsQuantityAttribute()
    {
        $total = 0;

        foreach ($this->items ?? [] as $item) {
            $total += $item->quantity ?? 0;
        }

        return $this->attributes['itemsQuantity'] = $total;
    }

    /**
     * Get the total amount for all the Items in the cart
     *
     * @return int
     */
    public function getTotalAttribute()
    {
        $amount = 0;
        foreach ($this->items as $item) {
            $amount += (int)$item->amount * $item->quantity;
        }
        return $this->attributes['total'] = $amount;
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
        if ($hasItems = $this->items ?? []) {
            $hasItems = [
                'items' => $this->items,
                'total' => $this->total,
                'itemsQuantity' => $this->itemsQuantity,
            ];
        }

        $attributes = array_merge($this->attributes, $hasItems, [
            'externalUniqueNumber' => $this->externalUniqueNumber,
        ]);

        if ($this->hideSecrets) {
            $attributes = Helpers::arrayExcept($attributes, ['appKey', 'apiKey', 'signature']);
        }

        return $attributes ?? $this->attributes;
    }
}