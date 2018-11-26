<?php

namespace Transbank\Wrapper\Transactions\Concerns;

use Transbank\Wrapper\Helpers\Helpers;
use Transbank\Wrapper\Transactions\Item;

trait HasItems
{
    /**
     * Items in the Transaction
     *
     * @var array
     */
    protected $items = [];

    /**
     * Sets Items from the constructor
     *
     * @param array $items
     */
    protected function setItemsFromConstruct(array $items)
    {
        // To know if the user is passing just one item, we will see if the
        // keys are not numeric. If they are, we will add the array on
        // top of the item so the OnepayTransaction can be made
        if (count($items) > 1 && !Helpers::isNumericArray($items)) {
            $items = [$items];
        }

        $this->addItems($items);
    }

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
        if (is_array($item)) {
            return new Item(array_merge($item, $this->itemDefaults ?? []));
        }

        // We did nothing, so we return nothing
        return null;
    }

    /**
     * Add an Item, or multiple Items
     *
     * @param array $items
     */
    public function addItem(...$items)
    {
        // If we just added a single array of Items, and the children are numeric,
        // then we will use the only key available
        if (count($items) === 1 && Helpers::isNumericArray($items)) {
            $items = $items[0];
        }

        foreach ($items as $item) {
            // Add the Item only if we can parse it
            if ($item = $this->parseItem($item)) {
                $this->items[] = $item;
            }
        }
    }

    /**
     * Add multiple items. Alias for addItem().
     *
     * @param mixed ...$items
     */
    public function addItems($items)
    {
        $this->addItem($items);
    }

    /**
     * Return all the Items in the OnepayTransaction
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Gets a particular Item from the OnepayTransaction by its Key
     *
     * @param int $key
     * @return mixed|null
     */
    public function getItem(int $key)
    {
        return $this->items[$key] ?? null;
    }

    /**
     * Get an Item by its exact description
     *
     * @param string $description
     * @return mixed|null
     */
    public function getItemByDescription(string $description)
    {
        foreach ($this->items as $item) {
            if ($item->description === $description) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Get an Item Key by its Description
     *
     * @param string $description
     * @return int|null|string
     */
    public function getItemKeyByDescription(string $description)
    {
        foreach ($this->items as $key => $item) {
            if ($item->description === $description) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Deletes an Item by its Key
     *
     * @param int $key
     * @return bool
     */
    public function deleteItem(int $key)
    {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
            return true;
        }
        return false;
    }

    /**
     * Deletes an Item by its Description
     *
     * @param string $description
     * @return bool
     */
    public function deleteItemByDescription(string $description)
    {
        if ($key = $this->getItemKeyByDescription($description)) {
            return $this->deleteItem($key);
        }
        return false;
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
            foreach ($attributes as $attribute => $value) {
                $this->items[$key]->$attribute = $value;
            }
            // Delete the updated item when its quantity is zero
            if ($this->items[$key]->quantity < 1) {
                $this->deleteItem($key);
                return false;
            }
            return $this->items[$key];
        }
        return false;
    }

    /**
     * Replaces completely an Item with another
     *
     * @param int $key
     * @param $item
     * @return bool
     */
    public function replaceItem(int $key, $item)
    {
        if (isset($this->items[$key])) {
            return !!($this->items[$key] = $this->parseItem($item));
        }
        return false;
    }

    /**
     * Count all Items in the OnepayTransaction
     *
     * @return int
     */
    public function countItems()
    {
        return count($this->items);
    }


    /**
     * Clears the Items array
     *
     * @return void
     */
    public function clearItems()
    {
        $this->items = [];
    }

    /**
     * Reindex the Items array
     *
     * @return void
     */
    public function reindexItems()
    {
        $this->items = array_values($this->items);
    }
}