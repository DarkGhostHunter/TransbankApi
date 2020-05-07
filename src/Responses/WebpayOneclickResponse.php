<?php

namespace DarkGhostHunter\TransbankApi\Responses;

use DarkGhostHunter\TransbankApi\Helpers\Helpers;

class WebpayOneclickResponse extends AbstractResponse
{
    /**
     * Token key for redirection
     *
     * @var string
     */
    protected $tokenName = 'TBK_TOKEN';

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
        switch (true) {
            case (bool)$this->{$this->tokenName}:
            case (bool)$this->reversed:
            case $this->responseCode === 0:
                $this->isSuccess = true;
                break;
            case count($this->attributes) === 1 && (Helpers::arrayFirst($this->attributes)) === true:
                $this->isSuccess = true;
                $this->attributes = [];
                break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Getters and Setters
    |--------------------------------------------------------------------------
    */

    /**
     * Catch the `urlWebpay` from Oneclick responses and fill it as `url`
     *
     * @param string $urlWebpay
     */
    public function setUrlWebpayAttribute(string $urlWebpay)
    {
        $this->attributes['url'] = $urlWebpay;
    }
}