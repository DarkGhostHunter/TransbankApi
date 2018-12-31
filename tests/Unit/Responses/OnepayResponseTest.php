<?php

namespace Tests\Unit\Responses;

use DarkGhostHunter\TransbankApi\Responses\OnepayResponse;
use PHPUnit\Framework\TestCase;

class OnepayResponseTest extends TestCase
{

    /** @var OnepayResponse */
    protected $response;

    protected function setUp()
    {
        $this->response = new OnepayResponse(['foo' => 'bar']);
    }

    public function testToArray()
    {
        $this->assertIsArray($this->response->toArray());
        $this->assertArrayHasKey('foo', $this->response->toArray());
    }

    public function testDynamicallySetSuccessStatus()
    {
        $this->response->dynamicallySetSuccessStatus();

        $this->assertTrue($this->response->isSuccess());
    }
}
