<?php

namespace Tests\Unit\Clients\Webpay;

use DarkGhostHunter\TransbankApi\Clients\Webpay\WebpayClient;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use PHPUnit\Framework\TestCase;

class WebpayClientTest extends TestCase
{

    public function testBoot()
    {
        $client = new class(true, new Fluent(['privateKey' => 'foo', 'publicCert' => 'bar'])) extends WebpayClient {
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
        $client = new class(true, new Fluent(['foo' => 'bar'])) extends WebpayClient {};

        $this->assertNull($client->getEndpointType());
        $client->setEndpointType('foo');
        $this->assertEquals('foo', $client->getEndpointType());
    }
}
