<?php

namespace DarkGhostHunter\TransbankApi\Contracts;

/**
 * Interface ResponseInterface
 *
 * Once Transbank returns a response from their systems, this contract allows the Service to
 * understand what is returned, if the response is a success (or failed), and to display
 * the error in something understandable.
 *
 * @package DarkGhostHunter\TransbankApi\Contracts
 */
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
     * Sets if the Result was successful or not
     *
     * @return void
     */
    public function setStatus();
}