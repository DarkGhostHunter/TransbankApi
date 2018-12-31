<?php

namespace Tests\Unit\Responses;

use DarkGhostHunter\TransbankApi\Responses\WebpayPatpassResponse;
use PHPUnit\Framework\TestCase;

class WebpayPatpassResponseTest extends TestCase
{

    public function testDynamicallySetSuccessStatus()
    {
        $response = new WebpayPatpassResponse(['token' => 'test-token']);
        $response->dynamicallySetSuccessStatus();

        $this->assertTrue($response->isSuccess());

        $response = new WebpayPatpassResponse([]);
        $response->dynamicallySetSuccessStatus();

        $this->assertFalse($response->isSuccess());

        $response = new WebpayPatpassResponse([
            'detailOutput' => (object)['responseCode' => 0]
        ]);
        $response->dynamicallySetSuccessStatus();

        $this->assertTrue($response->isSuccess());

        $response = new WebpayPatpassResponse([
            'detailOutput' => (object)['responseCode' => 1]
        ]);
        $response->dynamicallySetSuccessStatus();

        $this->assertFalse($response->isSuccess());


    }
}
