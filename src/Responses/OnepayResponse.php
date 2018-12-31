<?php

namespace DarkGhostHunter\TransbankApi\Responses;

use DarkGhostHunter\TransbankApi\Helpers\Helpers;

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
    public function dynamicallySetSuccessStatus()
    {
        $this->isSuccess = true;
    }

    /**
     * Transform the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return Helpers::arrayExcept($this->attributes, $this->hidden);
    }
}