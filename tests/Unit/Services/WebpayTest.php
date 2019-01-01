<?php

namespace Tests\Unit\Services;

use DarkGhostHunter\TransbankApi\Adapters\WebpayAdapter;
use DarkGhostHunter\TransbankApi\Exceptions\Credentials\CredentialsNotReadableException;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\RetrievingNoTransactionTypeException;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\Helpers\Helpers;
use DarkGhostHunter\TransbankApi\Responses\AbstractResponse;
use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use DarkGhostHunter\TransbankApi\Transbank;
use DarkGhostHunter\TransbankApi\Webpay;
use PHPUnit\Framework\TestCase;

class WebpayTest extends TestCase
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
            ->andReturn([ 'foo' => 'bar' ]);

        $this->mockTransbank->shouldReceive('getCredentials')->once()
            ->with('webpay')
            ->andReturn(new Fluent([ 'baz' => 'qux' ]));

        $this->mockTransbank->shouldReceive('isProduction')->once()
            ->andReturnTrue();

        $this->mockTransbank->shouldReceive('getEnvironment')->once()
            ->andReturn('production');

        $this->mockAdapter = \Mockery::mock(WebpayAdapter::class);

        $this->mockAdapter->shouldReceive('setCredentials')
            ->andReturn(['foo' => 'bar']);

        $this->webpay = new Webpay($this->mockTransbank);

        $this->webpay->setAdapter($this->mockAdapter);
    }

    public function testGetTransaction()
    {
        $this->mockAdapter->shouldReceive('retrieveAndConfirm')
            ->with('mock-transaction', 'mock-type')
            ->andReturnUsing(function ($transaction, $type) {
                return array_merge(func_get_args(), [
                    'foo' => 'bar'
                ]);
            });

        $transaction = $this->webpay->getTransaction('mock-transaction', 'mock-type');

        $this->assertInstanceOf(AbstractResponse::class, $transaction);
        $this->assertEquals('bar', $transaction->foo);
    }

    public function testExceptionOnGetTransactionWithoutType()
    {
        $this->expectException(RetrievingNoTransactionTypeException::class);

        $this->webpay->getTransaction('mock-transaction', null);
    }

    public function testRetrieveTransaction()
    {
        $this->mockAdapter->shouldReceive('retrieve')
            ->with('mock-transaction', 'mock-type')
            ->andReturnUsing(function ($transaction, $type) {
                return array_merge(func_get_args(), [
                    'foo' => 'bar'
                ]);
            });

        $transaction = $this->webpay->retrieveTransaction('mock-transaction', 'mock-type');

        $this->assertInstanceOf(AbstractResponse::class, $transaction);
        $this->assertEquals('bar', $transaction->foo);
    }

    public function testConfirmTransaction()
    {
        $this->mockAdapter->shouldReceive('confirm')
            ->once()
            ->with('mock-transaction', 'mock-type')
            ->andReturnTrue();

        $transaction = $this->webpay->confirmTransaction('mock-transaction', 'mock-type');

        $this->assertTrue($transaction);

        $this->mockAdapter->shouldReceive('confirm')
            ->once()
            ->with('mock-transaction', 'mock-type')
            ->andReturn([
                'foo' => 'bar'
            ]);

        $transaction = $this->webpay->confirmTransaction('mock-transaction', 'mock-type');

        $this->assertInstanceOf(AbstractResponse::class, $transaction);
        $this->assertEquals('bar', $transaction->foo);
    }

    public function testGetsIntegrationCredentials()
    {
        $transbank = \Mockery::mock(Transbank::class);
        $transbank->shouldReceive('getDefaults')->once()
            ->andReturn(['foo' => 'bar']);
        $transbank->shouldReceive('getCredentials')->once()
            ->andReturnNull();
        $transbank->shouldReceive('isProduction')->once()
            ->andReturnFalse();
        $transbank->shouldReceive('getEnvironment')->once()
            ->andReturn('integration');

        $adapter = \Mockery::mock(WebpayAdapter::class);

        $adapter->shouldReceive('setCredentials')
            ->andReturn(['foo' => 'bar']);
        $adapter->shouldReceive('retrieveAndConfirm')
            ->andReturn(['foo' => 'bar']);

        $webpay = new Webpay($transbank);

        $webpay->setAdapter($adapter);

        $transaction = $webpay->getTransaction('transaction', 'oneclick');
        $this->assertInstanceOf(AbstractResponse::class, $transaction);

        $transaction = $webpay->getTransaction('transaction', 'defer');
        $this->assertInstanceOf(AbstractResponse::class, $transaction);
        $transaction = $webpay->getTransaction('transaction', 'capture');
        $this->assertInstanceOf(AbstractResponse::class, $transaction);
        $transaction = $webpay->getTransaction('transaction', 'nullify');
        $this->assertInstanceOf(AbstractResponse::class, $transaction);

        $transaction = $webpay->getTransaction('transaction', 'mall');
        $this->assertInstanceOf(AbstractResponse::class, $transaction);

        $transaction = $webpay->getTransaction('transaction', 'test');
        $this->assertInstanceOf(AbstractResponse::class, $transaction);
    }

    public function testExceptionOnUnreadableIntegrationCredentials()
    {
        $this->expectException(CredentialsNotReadableException::class);

        $transbank = \Mockery::mock(Transbank::class);
        $transbank->shouldReceive('getDefaults')->once()
            ->andReturn(['foo' => 'bar']);
        $transbank->shouldReceive('getCredentials')->once()
            ->andReturnNull();
        $transbank->shouldReceive('isProduction')->once()
            ->andReturnFalse();
        $transbank->shouldReceive('getEnvironment')->once()
            ->andReturn('anything');

        $adapter = \Mockery::mock(WebpayAdapter::class);

        $adapter->shouldReceive('setCredentials')
            ->andReturn(['foo' => 'bar']);
        $adapter->shouldReceive('retrieveAndConfirm')
            ->andReturn(['foo' => 'bar']);

        $webpay = new Webpay($transbank);

        $webpay->setAdapter($adapter);

        $webpay->getTransaction('transaction', 'test');
    }


}
