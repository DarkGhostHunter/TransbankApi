<?php

namespace DarkGhostHunter\TransbankApi\Responses;

class OnepayResponse extends AbstractResponse
{
    /**
     * Attributes to hide on serialization (JSON, array)
     *
     * @var array
     */
    protected $hidden = [
        'signature'
    ];

    /**
     * Token Name for Forms
     *
     * @var string
     */
    protected $tokenName = 'TBK_TOKEN';


    /**
     * Detect if the Result was successful or not
     *
     * @return void
     */
    public function setStatus()
    {
        if (!($this->isSuccess = $this->responseCode === 'OK')) {
            $this->errorCode = $this->responseCode;
        };
    }
}