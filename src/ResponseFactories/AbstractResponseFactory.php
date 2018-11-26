<?php

namespace DarkGhostHunter\TransbankApi\ResponseFactories;

use DarkGhostHunter\TransbankApi\AbstractService;

abstract class AbstractResponseFactory
{
    /**
     * Service using this Factory
     *
     * @var \DarkGhostHunter\TransbankApi\AbstractService|\DarkGhostHunter\TransbankApi\Webpay|\DarkGhostHunter\TransbankApi\Onepay
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