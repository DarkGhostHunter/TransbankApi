<?php

namespace DarkGhostHunter\TransbankApi\Transactions;

use DarkGhostHunter\TransbankApi\AbstractService;
use DarkGhostHunter\TransbankApi\Contracts\ServiceInterface;
use DarkGhostHunter\TransbankApi\Contracts\TransactionInterface;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;

abstract class AbstractServiceTransaction extends Fluent implements TransactionInterface
{
    /**
     * Service this Transaction uses
     *
     * @var AbstractService
     */
    protected $service;

    /**
     * Type of Transaction
     *
     * @var string
     */
    protected $type;

    /**
     * Determines if the Transaction was sent to Transbank
     *
     * @var bool
     */
    protected $performed = false;

    /**
     * Saves the Transaction Response
     *
     * @var \DarkGhostHunter\TransbankApi\Responses\AbstractResponse
     */
    protected $response;

    /**
     * Performs the transaction commitment to Transbank
     *
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse
     */
    protected function performCommit()
    {
        // Fill for default attributes
        if (method_exists($this, $method = 'fillEmptyAttributes')
            && is_callable([$this, $method])) {
            call_user_func([$this, $method]);
        }

        // Do any logic before getting the result
        if (method_exists($this, $method = 'performPreLogic')
            && is_callable([$this, $method])) {
            call_user_func([$this, $method]);
        }

        if ($this->response = $this->service->commit($this)) {
            $this->performed = true;
        };

        // Do any logic before getting the result
        if (method_exists($this, $method = 'performPostLogic')
            && is_callable([$this, $method])) {
            call_user_func([$this, $method]);
        }

        return $this->response;
    }

    /**
     * Set default attributes for the Item
     *
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        $this->attributes = array_merge($defaults, $this->attributes);
    }

    /**
     * Set the Transaction type
     *
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * Return the Transaction type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the Service to be used for this Transaction
     *
     * @param ServiceInterface $service
     */
    public function setService(ServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Returns the Service used by this Transaction
     *
     * @return ServiceInterface
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Commits the transaction to Transbank and return a AbstractResult
     *
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse
     */
    public function commit()
    {
        return $this->performed ? $this->response : $this->performCommit();
    }

    /**
     * Forcefully commits the transaction to Transbank
     *
     * @return \DarkGhostHunter\TransbankApi\Responses\AbstractResponse
     */
    public function forceCommit()
    {
        return $this->performCommit();
    }
}