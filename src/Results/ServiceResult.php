<?php

namespace Transbank\Wrapper\Results;

use Transbank\Wrapper\Contracts\ResultInterface;
use Transbank\Wrapper\Helpers\Fluent;

class ServiceResult extends Fluent implements ResultInterface
{
    /**
     * File where all the translation lines reside
     *
     * @var string
     */
    const ERROR_LIST_FILE = __DIR__ . '/../../errors/translation.php';

    /**
     * Loaded list of errors that all TransactionResults share
     *
     * @var array
     */
    protected static $error_list;

    /**
     * Determines if the transaction was a success
     *
     * @var bool
     */
    protected $isSuccess = false;

    /**
     * Translated error code
     *
     * @var array
     */
    protected $error;

    /**
     * List of errors to query
     *
     * @var string
     */
    protected $errorList;

    /**
     * Token Name for Forms
     *
     * @var string
     */
    protected $tokenName = 'token';

    /**
     * Returns the loaded array of Error translations (or loads it)
     *
     * @return array|bool|string
     */
    protected static function getLoadedErrorList()
    {
        if (!self::$error_list) {
            self::$error_list = file_get_contents(self::ERROR_LIST_FILE);
        }
        return self::$error_list;
    }
    /**
     * Returns if the transaction was a success
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->isSuccess;
    }

    /**
     * Returns if the transaction was a failure
     *
     * @return bool
     */
    public function isFailure()
    {
        return !$this->isSuccess();
    }

    /**
     * Returns the Error Code if the transaction was a failure
     *
     * @return string
     */
    public function getErrorCode()
    {
        return key($this->error);
    }

    /**
     * Returns the error as an understandable text string
     *
     * @return mixed
     */
    public function getErrorForHumans()
    {
        if (!$this->error) {
            $this->error = $this->setError();
        }
        return $this->error[0];
    }

    /**
     * Locates and Sets the error
     *
     * @param $error
     * @return void
     */
    public function setErrorCode($error)
    {
        $this->error = $error;
    }

    /**
     * Return the token name for forms
     *
     * @return string
     */
    public function getTokenName()
    {
        return $this->tokenName;
    }
}