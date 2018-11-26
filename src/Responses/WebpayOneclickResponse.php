<?php

namespace DarkGhostHunter\TransbankApi\Responses;

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
    public function setStatus()
    {
        switch (true) {
            case count($this->attributes) === 1 && $this->attributes[0] === true:
                $this->isSuccess = true;
                $this->attributes = [];
                break;
            case !!$this->token:
                $this->isSuccess = true;
                break;
            case $this->responseCode === 0:
                $this->isSuccess = true;
                break;
            case $this->reversed:
                $this->isSuccess = true;
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