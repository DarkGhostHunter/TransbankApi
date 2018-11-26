<?php

namespace Transbank\Wrapper\ResponseFactories;

use Transbank\Wrapper\AbstractService;

abstract class AbstractResponseFactory
{
    /**
     * Service using this Factory
     *
     * @var \Transbank\Wrapper\AbstractService|\Transbank\Wrapper\Webpay|\Transbank\Wrapper\Onepay
     */
    protected $service;

    /**
     * AbstractResponseFactory constructor.
     *
     * @param AbstractService $service
     */
    public function __construct(AbstractService $service)
    {
        $this->service = $service;
    }

}