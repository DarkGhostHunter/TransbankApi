<?php

namespace Transbank\Wrapper\Contracts;

interface ResultInterface
{
    /**
     * Returns if the transaction was a success
     *
     * @return bool
     */
    public function isSuccess();

    /**
     * Returns if the transaction was a failure
     *
     * @return bool
     */
    public function isFailure();

    /**
     * Returns the Error Code if the transaction was a failure
     *
     * @return string
     */
    public function getErrorCode();

    /**
     * Locates and Sets the error
     *
     * @param $error
     * @return void
     */
    public function setErrorCode($error);

}