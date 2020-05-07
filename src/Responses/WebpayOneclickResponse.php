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
        if ($this->count() === 1 && (Helpers::arrayFirst($this->attributes) === true)) {
            $this->isSuccess = true;
            $this->attributes = [];
            return;
        }

        if ($this->{$this->tokenName} || $this->reversed || $this->responseCode === 0) {
            $this->isSuccess = true;
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