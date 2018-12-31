<?php

namespace Tests\Unit\Responses;

use DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse;
use PHPUnit\Framework\TestCase;

class WebpayPlusResponseTest extends TestCase
{

    public function testDynamicallySetSuccessStatusWithToken()
    {
        $response = new WebpayPlusResponse([
            'foo' => 'bar',
            'token' => 'mock-token'
        ]);

        $response->dynamicallySetSuccessStatus();

        $this->assertTrue($response->isSuccess());

        $response = new WebpayPlusResponse([
            'foo' => 'bar',
        ]);

        $response->dynamicallySetSuccessStatus();

        $this->assertFalse($response->isSuccess());
    }

    public function testDynamicallySetSuccessStatusWithDetailedOutput()
    {
        $response = new WebpayPlusResponse([
            'foo' => 'bar',
            'detailOutput' => (object)['responseCode' => 0],
        ]);

        $response->dynamicallySetSuccessStatus();

        $this->assertTrue($response->isSuccess());

        $response = new WebpayPlusResponse([
            'foo' => 'bar',
            'detailOutput' => (object)['responseCode' => 1],
        ]);

        $response->dynamicallySetSuccessStatus();

        $this->assertFalse($response->isSuccess());
    }
}
