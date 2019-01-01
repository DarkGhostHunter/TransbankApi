<?php

namespace Tests\Unit\Responses;

use DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse;
use PHPUnit\Framework\TestCase;

class WebpayPlusMallResponseTest extends TestCase
{

    public function testDynamicallySetSuccessStatusWithToken()
    {
        $response = new WebpayPlusMallResponse([
            'token' => 'test-token'
        ]);

        $response->dynamicallySetSuccessStatus();

        $this->assertTrue($response->isSuccess());

        $response = new WebpayPlusMallResponse([]);

        $response->dynamicallySetSuccessStatus();

        $this->assertFalse($response->isSuccess());
    }

    public function testDynamicallySetSuccessStatusWithItems()
    {
        $response = new WebpayPlusMallResponse([
            'detailOutput' => [
                (object)['responseCode' => 0],
                (object)['responseCode' => 0],
                (object)['responseCode' => 0],
            ]
        ]);

        $response->dynamicallySetSuccessStatus();

        $this->assertTrue($response->isSuccess());

        $response = new WebpayPlusMallResponse([
            'detailOutput' => [
                (object)['responseCode' => 0],
                (object)['responseCode' => 1],
                (object)['responseCode' => 0],
            ]
        ]);

        $response->dynamicallySetSuccessStatus();

        $this->assertFalse($response->isSuccess());

        $response = new WebpayPlusMallResponse([
            'detailOutput' => (object)['responseCode' => 0]
        ]);

        $response->dynamicallySetSuccessStatus();

        $this->assertFalse($response->isSuccess());
    }

    public function testGetSuccessfulTotal()
    {
        $response = new WebpayPlusMallResponse([
            'detailOutput' => [
                (object)['responseCode' => 0, 'amount' => 4990],
                (object)['responseCode' => 1, 'amount' => 4990],
                (object)['responseCode' => 0, 'amount' => 4990],
            ]
        ]);

        $this->assertEquals(4990 * 2, $response->getSuccessfulTotal());
    }

    public function testGetSuccessfulOrdersOrGetSuccessfulItems()
    {
        $response = new WebpayPlusMallResponse([
            'detailOutput' => [
                (object)['responseCode' => 0, 'amount' => 4990],
                (object)['responseCode' => 1, 'amount' => 4990],
                (object)['responseCode' => 0, 'amount' => 4990],
            ]
        ]);

        $this->assertCount(2, $response->getSuccessfulOrders());
        $this->assertCount(2, $response->getSuccessfulItems());
    }

    public function testGetTotal()
    {
        $response = new WebpayPlusMallResponse([
            'detailOutput' => [
                (object)['responseCode' => 0, 'amount' => 4990],
                (object)['responseCode' => 1, 'amount' => 4990],
                (object)['responseCode' => 0, 'amount' => 4990],
            ]
        ]);

        $this->assertEquals(4990 * 3, $response->getTotal());
    }

    public function testGetOrdersOrGetItems()
    {
        $response = new WebpayPlusMallResponse([
            'detailOutput' => $array = [
                (object)['responseCode' => 0, 'amount' => 4990],
                (object)['responseCode' => 1, 'amount' => 4990],
                (object)['responseCode' => 0, 'amount' => 4990],
            ]
        ]);

        $this->assertEquals($array, $response->getOrders());
        $this->assertEquals($array, $response->getItems());
    }

    public function testGetOrderErrorForHumansOrGetItemErrorForHumans()
    {
        $response = new WebpayPlusMallResponse([
            'detailOutput' => [
                (object)['responseCode' => 0, 'amount' => 4990],
                (object)['responseCode' => 1, 'errorCode' => 18, 'amount' => 4990],
                (object)['responseCode' => 0, 'amount' => 4990],
                (object)['responseCode' => 1, 'errorCode' => 99999, 'amount' => 4990],
            ]
        ]);

        $this->assertEquals('ERR_SERVIDOR_COMERCIO', $response->getOrderErrorForHumans(1));
        $this->assertEquals('ERR_SERVIDOR_COMERCIO', $response->getItemErrorForHumans(1));
        $this->assertNull($response->getOrderErrorForHumans(3));
        $this->assertNull($response->getOrderErrorForHumans(5));
    }

    public function testGetFailedOrdersOrGetFailedItems()
    {
        $failed = [
            (object)['responseCode' => 1, 'errorCode' => 18, 'amount' => 4990],
            (object)['responseCode' => 1, 'errorCode' => 1, 'amount' => 4990],
        ];

        $response = new WebpayPlusMallResponse([
            'detailOutput' => [
                (object)['responseCode' => 0, 'amount' => 4990],
                (object)['responseCode' => 1, 'errorCode' => 18, 'amount' => 4990],
                (object)['responseCode' => 0, 'amount' => 4990],
                (object)['responseCode' => 1, 'errorCode' => 1, 'amount' => 4990],
            ]
        ]);

        $this->assertEquals($failed, $response->getFailedOrders());
        $this->assertEquals($failed, $response->getFailedItems());
    }

    public function testGetFailedTotal()
    {
        $response = new WebpayPlusMallResponse([
            'detailOutput' => [
                (object)['responseCode' => 0, 'amount' => 4990],
                (object)['responseCode' => 1, 'errorCode' => 18, 'amount' => 1990],
                (object)['responseCode' => 0, 'amount' => 4990],
                (object)['responseCode' => 1, 'errorCode' => 1, 'amount' => 1990],
            ]
        ]);

        $this->assertEquals(1990 * 2, $response->getFailedTotal());
    }
}
