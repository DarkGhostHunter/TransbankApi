<?php

namespace Tests\Unit\Responses;

use DarkGhostHunter\TransbankApi\Responses\AbstractResponse;
use PHPUnit\Framework\TestCase;

class AbstractResponseTest extends TestCase
{
    /** @var AbstractResponse */
    protected $response;

    protected function setUp() : void
    {
        $this->response = new class extends AbstractResponse {
            protected $tokenName = 'old-token';
            protected $listKey = 'webpay.plus';
            protected $errorCode = -8;
            public function dynamicallySetSuccessStatus()
            {
                $this->isSuccess = true;
            }
        };
    }

    public function testGetAndSetTokenName()
    {
        $this->assertEquals('old-token', $this->response->getTokenName());
        $this->response->setTokenName('new-token');
        $this->assertEquals('new-token', $this->response->getTokenName());
    }

    public function testDynamicallySetSuccessStatus()
    {
        $this->response->dynamicallySetSuccessStatus();
        $this->assertTrue($this->response->isSuccess());
    }

    public function testGetErrorForHumans()
    {
        $errorCode = 'Rubro no autorizado.';

        $this->assertEquals($errorCode, $this->response->getErrorForHumans());
    }

    public function testSuccess()
    {
        $this->response->dynamicallySetSuccessStatus();

        $this->assertTrue($this->response->isSuccess());
        $this->assertFalse($this->response->isFailed());
    }

    public function testGetAndSetType()
    {
        $this->response->setType('mock-type');
        $this->assertEquals('mock-type', $this->response->getType());
    }

    public function testGetErrorCode()
    {
        $this->assertEquals(-8, $this->response->getErrorCode());
    }
}
