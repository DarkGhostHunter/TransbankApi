<?php

namespace Tests\Unit\TransactionFactories;

use DarkGhostHunter\TransbankApi\Adapters\WebpayAdapter;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\Responses\WebpayOneclickResponse;
use DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse;
use DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse;
use DarkGhostHunter\TransbankApi\Transactions\WebpayMallTransaction;
use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use DarkGhostHunter\TransbankApi\Transbank;
use DarkGhostHunter\TransbankApi\Webpay;
use PHPUnit\Framework\TestCase;

class WebpayTransactionFactoryTest extends TestCase
{
    /** @var Transbank&\Mockery\MockInterface */
    protected $mockTransbank;

    /** @var Webpay */
    protected $webpay;

    /** @var WebpayAdapter&\Mockery\MockInterface */
    protected $mockAdapter;

    protected function setUp()
    {
        $this->mockTransbank = \Mockery::mock(Transbank::class);
        $this->mockTransbank->shouldReceive('getDefaults')->once()
            ->andReturn(['foo' => 'bar']);
        $this->mockTransbank->shouldReceive('getCredentials')->once()
            ->with('webpay')
            ->andReturn(new Fluent(['foo' => 'bar']));
        $this->mockTransbank->shouldReceive('isProduction')->once()
            ->andReturnTrue();
        $this->mockTransbank->shouldReceive('getEnvironment')
            ->andReturn('production');

        $this->mockAdapter = \Mockery::mock(WebpayAdapter::class);

        $this->webpay = new Webpay($this->mockTransbank);
        $this->webpay->setAdapter($this->mockAdapter);
    }

    public function testCreateUnregistration()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $unregistration = $this->webpay->createUnregistration([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $unregistration);
        $this->assertEquals('bar', $unregistration->foo);
    }

    public function testCreateMallDefer()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $mallDefer = $this->webpay->createMallDefer([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayPlusMallResponse::class, $mallDefer);
        $this->assertEquals('bar', $mallDefer->foo);
    }

    public function testCreateMallCharge()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $mallCharge = $this->webpay->createMallCharge([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $mallCharge);
        $this->assertEquals('bar', $mallCharge->foo);
    }

    public function testCreateNormal()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $normal = $this->webpay->createNormal([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayPlusResponse::class, $normal);
        $this->assertEquals('bar', $normal->foo);
    }

    public function testMakeMallCapture()
    {
        $mallCapture = $this->webpay->makeMallCapture([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayMallTransaction::class, $mallCapture);
        $this->assertEquals('bar', $mallCapture->foo);
    }

    public function testCreateMallReverseCharge()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $mallReverseCharge = $this->webpay->createMallReverseCharge([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $mallReverseCharge);
        $this->assertEquals('bar', $mallReverseCharge->foo);
    }

    public function testMakeMallReverseNullify()
    {
        $mallReverseNullify = $this->webpay->makeMallReverseNullify([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayMallTransaction::class, $mallReverseNullify);
        $this->assertEquals('bar', $mallReverseNullify->foo);

    }

    public function testCreateMallNormal()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $mallNormal = $this->webpay->createMallNormal([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayPlusMallResponse::class, $mallNormal);
        $this->assertEquals('bar', $mallNormal->foo);
    }

    public function testCreateMallNullify()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $mallNullify = $this->webpay->createMallNullify([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $mallNullify);
        $this->assertEquals('bar', $mallNullify->foo);
    }

    public function testCreateCapture()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $capture = $this->webpay->createCapture([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayPlusResponse::class, $capture);
        $this->assertEquals('bar', $capture->foo);
    }

    public function testMakeNormal()
    {
        $normal = $this->webpay->makeNormal([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayTransaction::class, $normal);
        $this->assertEquals('bar', $normal->foo);
    }

    public function testMakeRegistration()
    {
        $registration = $this->webpay->makeRegistration([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayTransaction::class, $registration);
        $this->assertEquals('bar', $registration->foo);
    }

    public function testMakeMallNormal()
    {
        $mallNormal = $this->webpay->makeMallNormal([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayMallTransaction::class, $mallNormal);
        $this->assertEquals('bar', $mallNormal->foo);
    }

    public function testCreateDefer()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $defer = $this->webpay->createDefer([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayPlusResponse::class, $defer);
        $this->assertEquals('bar', $defer->foo);
    }

    public function testMakeNullify()
    {
        $nullify = $this->webpay->makeNullify([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayTransaction::class, $nullify);
        $this->assertEquals('bar', $nullify->foo);
    }

    public function testMakeMallDefer()
    {
        $mallDefer = $this->webpay->makeMallDefer([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayMallTransaction::class, $mallDefer);
        $this->assertEquals('bar', $mallDefer->foo);
    }

    public function testCreateMallCapture()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $mallCapture = $this->webpay->createMallCapture([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayPlusMallResponse::class, $mallCapture);
        $this->assertEquals('bar', $mallCapture->foo);
    }

    public function testMakeMallReverseCharge()
    {
        $reverseCharge = $this->webpay->makeMallReverseCharge([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayMallTransaction::class, $reverseCharge);
        $this->assertEquals('bar', $reverseCharge->foo);
    }

    public function testMakeUnregistration()
    {
        $unregistration = $this->webpay->makeUnregistration([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayTransaction::class, $unregistration);
        $this->assertEquals('bar', $unregistration->foo);
    }

    public function testCreateReverseCharge()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $reverseCharge = $this->webpay->createReverseCharge([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $reverseCharge);
        $this->assertEquals('bar', $reverseCharge->foo);
    }

    public function testMakeCapture()
    {
        $capture = $this->webpay->makeCapture([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayTransaction::class, $capture);
        $this->assertEquals('bar', $capture->foo);
    }

    public function testCreateNullify()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $nullify = $this->webpay->createNullify([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayPlusResponse::class, $nullify);
        $this->assertEquals('bar', $nullify->foo);
    }

    public function testCreateCharge()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $charge = $this->webpay->createCharge([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $charge);
        $this->assertEquals('bar', $charge->foo);
    }

    public function testMakeReverseCharge()
    {
        $reverseCharge = $this->webpay->makeReverseCharge([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayTransaction::class, $reverseCharge);
        $this->assertEquals('bar', $reverseCharge->foo);
    }

    public function testMakeMallCharge()
    {
        $mallCharge = $this->webpay->makeMallCharge([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayMallTransaction::class, $mallCharge);
        $this->assertEquals('bar', $mallCharge->foo);
    }

    public function testCreateMallReverseNullify()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $mallReverseNullify = $this->webpay->createMallReverseNullify([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $mallReverseNullify);
        $this->assertEquals('bar', $mallReverseNullify->foo);
    }

    public function testCreateRegistration()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once();
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                return $transaction->toArray();
            });

        $registration = $this->webpay->createRegistration([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $registration);
        $this->assertEquals('bar', $registration->foo);
    }

    public function testMakeCharge()
    {
        $charge = $this->webpay->makeCharge([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayTransaction::class, $charge);
        $this->assertEquals('bar', $charge->foo);
    }

    public function testMakeDefer()
    {
        $defer = $this->webpay->makeDefer([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayTransaction::class, $defer);
        $this->assertEquals('bar', $defer->foo);
    }

    public function testMakeMallNullify()
    {
        $mallNullify = $this->webpay->makeMallNullify([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(WebpayMallTransaction::class, $mallNullify);
        $this->assertEquals('bar', $mallNullify->foo);
    }
}
