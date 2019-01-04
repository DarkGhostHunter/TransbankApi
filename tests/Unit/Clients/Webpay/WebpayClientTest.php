<?php

namespace Tests\Unit\Clients\Webpay;

use DarkGhostHunter\Fluid\Fluid;
use DarkGhostHunter\TransbankApi\Clients\Webpay\WebpayClient;
use PHPUnit\Framework\TestCase;

class WebpayClientTest extends TestCase
{

    public function testBoot()
    {
        $client = new class(true, new Fluid(['privateKey' => 'foo', 'publicCert' => 'bar'])) extends WebpayClient {
            protected $endpointType = 'webpay';
        };

        $this->assertNull($client->getEndpoint());
        $this->assertNull($client->getConnector());

        $client->boot();

        $this->assertNotNull($client->getEndpoint());
        $this->assertNotNull($client->getConnector());
    }

    public function testEndpointType()
    {
        $client = new class(true, new Fluid(['foo' => 'bar'])) extends WebpayClient {};

        $this->assertNull($client->getEndpointType());
        $client->setEndpointType('foo');
        $this->assertEquals('foo', $client->getEndpointType());
    }
}
