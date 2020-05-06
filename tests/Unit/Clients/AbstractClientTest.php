<?php

namespace Tests\Unit\Clients;

use DarkGhostHunter\Fluid\Fluid;
use DarkGhostHunter\TransbankApi\Clients\AbstractClient;
use PHPUnit\Framework\TestCase;

class AbstractClientTest extends TestCase
{

    /** @var AbstractClient */
    protected $client;

    protected function setUp() : void
    {
        $this->client = new class(true, new Fluid(['foo' => 'bar'])) extends AbstractClient {
            protected function boot() {}
        };
    }

    public function testSetAndGetEndpoint()
    {
        $this->assertNull($this->client->getEndpoint());
        $this->client->setEndpoint('foo');
        $this->assertEquals('foo', $this->client->getEndpoint());
    }
}
