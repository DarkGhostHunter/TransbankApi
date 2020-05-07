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
        $foo = new WebpayOneclickResponse([true]);
        $foo->dynamicallySetSuccessStatus();

        $this->assertTrue($foo->isSuccess());

        $bar = new WebpayOneclickResponse([
            'urlWebpay' => $url = 'http://webpay4g.this.com/is-a-test/',
            'TBK_TOKEN' => $token = 'test-token',
        ]);
        $bar->dynamicallySetSuccessStatus();

        $this->assertTrue($bar->isSuccess());

        $quz = new WebpayOneclickResponse(['responseCode' => 0]);
        $quz->dynamicallySetSuccessStatus();


        var_dump($quz->attributes);
        var_dump($quz->{$quz->tokenName});
        var_dump($quz->reversed);
        var_dump($quz->responseCode);

        $this->assertTrue($quz->isSuccess());

        $qux = new WebpayOneclickResponse(['responseCode' => 1]);
        $qux->dynamicallySetSuccessStatus();

        $this->assertFalse($qux->isSuccess());

        $quuz = new WebpayOneclickResponse(['reversed' => 'anything']);
        $quuz->dynamicallySetSuccessStatus();

        $this->assertTrue($quuz->isSuccess());
    }
}
