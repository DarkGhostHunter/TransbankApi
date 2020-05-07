<?php

namespace DarkGhostHunter\TransbankApi\Responses;

use DarkGhostHunter\Fluid\Fluid;
use DarkGhostHunter\TransbankApi\Contracts\ResponseInterface;

abstract class AbstractResponse extends Fluid implements ResponseInterface
{
    /**
     * File where all the error codes translation reside
     *
     * @var string
     */
    const TRANSLATION_LIST_FILE = __DIR__ . '/../../results/translation.php';

    /**
     * Loaded list of codes that all TransactionResults share
     *
     * @var array
     */
    protected static $translationList;

    /**
     * Token key for redirection
     *
     * @var string
     */
    protected $tokenName = 'token_ws';

    /**
     * Transaction Type of this Response, if applicable
     *
     * @var string
     */
    protected $type = null;

    /**
     * If the Response is a success
     *
     * @var bool
     */
    protected $isSuccess = false;

    /**
     * Error code
     *
     * @var string
     */
    protected $errorCode;

    /**
     * Translated Error Code
     *
     * @var string
     */
    protected $errorForHumans;

    /**
     * List of results to query for the translation
     *
     * @var string
     */
    protected $listKey;

    /*
    |--------------------------------------------------------------------------
    | Construction
    |--------------------------------------------------------------------------
    */

    /**
     * WebpayResponse constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Boot the response if it needed
        if (method_exists($this, 'boot') && is_callable([$this, 'boot'])) {
            call_user_func([$this, 'boot']);
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
    abstract public function dynamicallySetSuccessStatus();

    /*
    |--------------------------------------------------------------------------
    | Getters and Setters
    |--------------------------------------------------------------------------
    */

    /**
     * Gets the Token Name for redirection
     *
     * @return string
     */
    public function getTokenName()
    {
        return $this->tokenName;
    }

    /**
     * Sets the Token Name for redirection
     *
     * @param string $tokenName
     */
    public function setTokenName(string $tokenName)
    {
        $this->tokenName = $tokenName;
    }

    /**
     * Gets the Transaction Type for the Response (if available)
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the Transaction Type for the Response (if available)
     *
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /*
    |--------------------------------------------------------------------------
    | Success indicator
    |--------------------------------------------------------------------------
    */

    /**
     * Return if the Response is a success
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->isSuccess;
    }

    /**
     * Returns if the Response was a failure
     *
     * @return bool
     */
    public function isFailed()
    {
        return !$this->isSuccess();
    }

    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the loaded array of Error translations (or loads it)
     *
     * @return array|bool|string
     */
    protected static function getLoadedErrorList()
    {
        return self::$translationList
            ? self::$translationList
            : self::$translationList = include_once(self::TRANSLATION_LIST_FILE);
    }

    /**
     * Returns the Error Code if the transaction was a failure
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Returns the error as an understandable text string
     *
     * @return mixed
     */
    public function getErrorForHumans()
    {
        return self::getLoadedErrorList()[$this->listKey][$this->errorCode] ?? null;
    }

}