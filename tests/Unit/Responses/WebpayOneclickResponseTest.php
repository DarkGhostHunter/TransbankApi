<?php

namespace Tests\Unit\Responses;

use DarkGhostHunter\TransbankApi\Responses\WebpayOneclickResponse;
use PHPUnit\Framework\TestCase;

class WebpayOneclickResponseTest extends TestCase
{
    public function testSetUrlWebpayAttribute()
    {
        $response = new WebpayOneclickResponse([
            'urlWebpay' => $url = 'http://webpay4g.this.com/is-a-test/',
            'TBK_TOKEN' => $token = 'test-token',
        ]);

        $this->assertEquals($url, $response->url);
        $this->assertEquals($token, $response->TBK_TOKEN);

    }

    public function testDynamicallySetSuccessStatus()
    {
        $response = new WebpayOneclickResponse([true]);
        $response->dynamicallySetSuccessStatus();

        $this->assertTrue($response->isSuccess());

        $response = new WebpayOneclickResponse([
            'urlWebpay' => $url = 'http://webpay4g.this.com/is-a-test/',
            'TBK_TOKEN' => $token = 'test-token',
        ]);
        $response->dynamicallySetSuccessStatus();

        $this->assertTrue($response->isSuccess());

        $response = new WebpayOneclickResponse(['responseCode' => 0]);
        $response->dynamicallySetSuccessStatus();

        $this->assertTrue($response->isSuccess());

        $response = new WebpayOneclickResponse(['responseCode' => 1]);
        $response->dynamicallySetSuccessStatus();

        $this->assertFalse($response->isSuccess());

        $response = new WebpayOneclickResponse(['reversed' => 'anything']);
        $response->dynamicallySetSuccessStatus();

        $this->assertTrue($response->isSuccess());
    }
}
