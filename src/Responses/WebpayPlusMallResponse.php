<?php

namespace DarkGhostHunter\TransbankApi\Responses;

class WebpayPlusMallResponse extends AbstractResponse
{

    /**
     * List of results to query for the translation
     *
     * @var string
     */
    protected $listKey = 'webpay.plus';

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

    /**
     * Boot the Mall Response
     *
     * @return void
     */
    protected function boot()
    {
        // Transform a single item to an array, if its only one item returned
        if ($this->detailOutput) {
            $this->detailOutput = is_array($this->detailOutput)
                ? $this->detailOutput
                : [$this->detailOutput];
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    /**
     * Detect if the Result was successful or not
     *
     * @return void
     */
    public function dynamicallySetSuccessStatus()
    {
        if ($this->token) {
            $this->isSuccess = true;
            return;
        }

        if ($this->detailOutput) {
            $this->isSuccess = $this->allItemsAreOk($this->detailOutput);
        }
    }

    /**
     * Detect if all the items returned in the response are successful
     *
     * @param array|null $items
     * @return bool
     */
    protected function allItemsAreOk(array $items)
    {
        foreach ($items as $item) {
            if ($item->responseCode !== 0) {
                return false;
            }
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Error Management
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the Order Error for Humans
     *
     * @param int $key
     * @return string|null
     */
    public function getOrderErrorForHumans(int $key)
    {
        // First let's see if the item index exists
        $item = $this->detailOutput[$key] ?? null;

        // If the item exists and the errorCode also exists, return the translated
        // error code only if you can find it. Otherwise the script will proceed
        // and eventually return null since it did not exists from the get go.
        if ($item && $item->errorCode) {
            return self::getLoadedErrorList()[$this->listKey][$item->errorCode] ?? null;
        }
        return null;
    }

    /**
     * Alias for getOrderErrorForHumans()
     *
     * @param int $key
     * @return string|null
     */
    public function getItemErrorForHumans(int $key)
    {
        return $this->getOrderErrorForHumans($key);
    }

    /*
    |--------------------------------------------------------------------------
    | Order (Item) management
    |--------------------------------------------------------------------------
    */

    /**
     * Return all the orders
     *
     * @return array
     */
    public function getOrders()
    {
        return $this->detailOutput;
    }

    /**
     * Alias for getOrders()
     *
     * @return mixed
     */
    public function getItems()
    {
        return $this->getOrders();
    }

    /**
     * Return only successful orders
     *
     * @return array
     */
    public function getSuccessfulOrders()
    {
        $successful = [];

        foreach ($this->getItems() as $item) {
            if ($item->responseCode === 0) {
                $successful[] = $item;
            }
        }

        return $successful;
    }

    /**
     * Alias for getSuccessfulOrders()
     *
     * @return array
     */
    public function getSuccessfulItems()
    {
        return $this->getSuccessfulOrders();
    }

    /**
     * Return all failed Orders
     *
     * @return array
     */
    public function getFailedOrders()
    {
        $successful = [];

        foreach ($this->getItems() as $item) {
            if ($item->responseCode !== 0) {
                $successful[] = $item;
            }
        }

        return $successful;
    }

    /**
     * Alias for getFailedOrders()
     *
     * @return array
     */
    public function getFailedItems()
    {
        return $this->getFailedOrders();
    }


    /**
     * Return the total amount of the whole transaction
     *
     * @return int
     */
    public function getTotal()
    {
        $total = 0;

        foreach ($this->getItems() as $item) {
            $total += $item->amount;
        }

        return $total;
    }

    /**
     * Return only the total amount from successful items
     *
     * @return int
     */
    public function getSuccessfulTotal()
    {
        $total = 0;

        foreach ($this->getSuccessfulItems() as $item) {
            $total += $item->amount;
        }

        return $total;
    }

    /**
     * Return only the total amount from failed items
     *
     * @return int
     */
    public function getFailedTotal()
    {
        $total = 0;

        foreach ($this->getFailedItems() as $item) {
            $total += $item->amount;
        }

        return $total;
    }
}