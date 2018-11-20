<?php

namespace Transbank\Wrapper\Results;

class OnepayResult extends ServiceResult
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
     * ServiceResult constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        // Sets the result success or failure
        $this->setIsSuccess($attributes);

        parent::__construct($attributes);
    }

    /**
     * Check if the Result is successful or not
     *
     * @param array $attributes
     * @return void
     */
    protected function setIsSuccess(array $attributes)
    {
        $this->isSuccess = isset($attributes['responseCode']) && $attributes['responseCode'] === 'OK';
    }
}