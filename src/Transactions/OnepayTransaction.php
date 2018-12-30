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
class OnepayTransaction extends AbstractTransaction
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
    | External Unique Number generation
    |--------------------------------------------------------------------------
    */

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

        // Set the time this is being committed as a timestamp
        $this->issuedAt = time();

        $this->itemsQuantity = $this->getItemsQuantityAttribute();
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
        if(is_string($item) && $array = json_decode($item, true)) {
            $item = $array;
        }

        if (is_array($item) && $item['quantity'] > 0) {
            return new Item(array_merge($this->itemDefaults,$item));
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Items attribute helpers functions
    |--------------------------------------------------------------------------
    */

    /**
     * Returns hoy many Items this WebpayClient has
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
        $attributes = null;
        if ($this->items) {
            $attributes = array_merge(
                array_merge(
                    $this->attributes,
                    [
                        'total' => $this->total,
                        'itemsQuantity' => $this->itemsQuantity
                    ]
                ),
                ['items' => $this->items]
            );
        }

        return $attributes ?? $this->attributes;
    }
}