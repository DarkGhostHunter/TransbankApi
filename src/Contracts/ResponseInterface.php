<?php

namespace DarkGhostHunter\TransbankApi\Contracts;

interface ResponseInterface
{
    /**
     * Return if the Response is a success
     *
     * @return bool
     */
    public function isSuccess();

    /**
     * Returns if the Response was a failure
     *
     * @return bool
     */
    public function isFailed();

    /**
     * Returns the Error Code if the transaction was a failure
     *
     * @return string
     */
    public function getErrorCode();

    /**
     * Detect if the Result was successful or not
     *
     * @return void
     */
    public function setStatus();
}