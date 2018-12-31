<?php

namespace Tests\Unit\ResponseFactory;

use DarkGhostHunter\TransbankApi\Adapters\WebpayAdapter;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\Responses\WebpayOneclickResponse;
use DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse;
use DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse;
use DarkGhostHunter\TransbankApi\Transbank;
use DarkGhostHunter\TransbankApi\Webpay;
use PHPUnit\Framework\TestCase;

class WebpayResponseFactoryTest extends TestCase
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

    public function testGetMallNormal()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once()
            ->andReturn(['foo' => 'bar']);
        $this->mockAdapter->shouldReceive('retrieveAndConfirm')->once()
            ->with('test-transaction', $type = 'plus.mall.normal')
            ->andReturn(['foo' => 'bar']);

        $mallNormal = $this->webpay->getMallNormal('test-transaction');

        $this->assertInstanceOf(WebpayPlusMallResponse::class, $mallNormal);
        $this->assertEquals('bar', $mallNormal->foo);
        $this->assertEquals($type, $mallNormal->getType());
    }

    public function testGetRegistration()
    {
        $this->mockAdapter->shouldReceive('setCredentials')
            ->andReturn(['foo' => 'bar']);
        $this->mockAdapter->shouldReceive('confirm')->once()
            ->with('test-transaction', $type = 'oneclick.confirm')
            ->andReturn(['foo' => 'bar']);

        $mallNormal = $this->webpay->getRegistration('test-transaction');

        $this->assertInstanceOf(WebpayOneclickResponse::class, $mallNormal);
        $this->assertEquals('bar', $mallNormal->foo);
        $this->assertEquals($type, $mallNormal->getType());

        $this->mockAdapter->shouldReceive('confirm')->once()
            ->with('test-transaction', $type = 'oneclick.confirm')
            ->andReturnTrue();

        $mallNormal = $this->webpay->getRegistration('test-transaction');

        $this->assertTrue($mallNormal);
    }

    public function testGetNormal()
    {
        $this->mockAdapter->shouldReceive('setCredentials')
            ->andReturn(['foo' => 'bar']);
        $this->mockAdapter->shouldReceive('retrieveAndConfirm')->once()
            ->with('test-transaction', $type = 'plus.normal')
            ->andReturn(['foo' => 'bar']);

        $mallNormal = $this->webpay->getNormal('test-transaction');

        $this->assertInstanceOf(WebpayPlusResponse::class, $mallNormal);
        $this->assertEquals('bar', $mallNormal->foo);
        $this->assertEquals($type, $mallNormal->getType());
    }

    public function testRetrieveMallNormal()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once()
            ->andReturn(['foo' => 'bar']);
        $this->mockAdapter->shouldReceive('retrieve')->once()
            ->with('test-transaction', $type = 'plus.mall.normal')
            ->andReturn(['foo' => 'bar']);

        $mallNormal = $this->webpay->retrieveMallNormal('test-transaction');

        $this->assertInstanceOf(WebpayPlusMallResponse::class, $mallNormal);
        $this->assertEquals('bar', $mallNormal->foo);
        $this->assertEquals($type, $mallNormal->getType());
    }

    public function testConfirmMallNormal()
    {
        $this->mockAdapter->shouldReceive('setCredentials')
            ->andReturn(['foo' => 'bar']);
        $this->mockAdapter->shouldReceive('confirm')->once()
            ->with('test-transaction', $type = 'plus.mall.normal')
            ->andReturnTrue();

        $mallNormal = $this->webpay->confirmMallNormal('test-transaction');

        $this->assertTrue($mallNormal);
    }

    public function testRetrieveNormal()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once()
            ->andReturn(['foo' => 'bar']);
        $this->mockAdapter->shouldReceive('retrieve')->once()
            ->with('test-transaction', $type = 'plus.normal')
            ->andReturn(['foo' => 'bar']);

        $normal = $this->webpay->retrieveNormal('test-transaction');

        $this->assertInstanceOf(WebpayPlusResponse::class, $normal);
        $this->assertEquals('bar', $normal->foo);
        $this->assertEquals($type, $normal->getType());
    }

    public function testConfirmNormal()
    {
        $this->mockAdapter->shouldReceive('setCredentials')
            ->andReturn(['foo' => 'bar']);
        $this->mockAdapter->shouldReceive('confirm')->once()
            ->with('test-transaction', $type = 'plus.normal')
            ->andReturnTrue();

        $normal = $this->webpay->confirmNormal('test-transaction');

        $this->assertTrue($normal);
    }
}
