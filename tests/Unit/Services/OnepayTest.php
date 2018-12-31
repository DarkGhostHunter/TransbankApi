<?php

namespace Tests\Unit\Services;

use DarkGhostHunter\TransbankApi\Adapters\OnepayAdapter;
use DarkGhostHunter\TransbankApi\Onepay;
use DarkGhostHunter\TransbankApi\Responses\OnepayResponse;
use DarkGhostHunter\TransbankApi\TransactionFactories\OnepayTransactionFactory;
use DarkGhostHunter\TransbankApi\Transactions\OnepayNullifyTransaction;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;
use DarkGhostHunter\TransbankApi\Transbank;
use PHPUnit\Framework\TestCase;

class OnepayTest extends TestCase
{
    /** @var Transbank&\Mockery\MockInterface */
    protected $mockTransbank;

    /** @var Onepay */
    protected $onepay;

    /** @var OnepayAdapter&\Mockery\MockInterface */
    protected $mockAdapter;

    protected function setUp()
    {
        $this->mockTransbank = \Mockery::mock(Transbank::class);

        $this->mockTransbank->shouldReceive('getDefaults') ->once()
            ->andReturn([ 'foo' => 'bar' ]);

        $this->mockTransbank->shouldReceive('getCredentials') ->once()
            ->andReturn([ 'baz' => 'qux' ]);

        $this->mockAdapter = \Mockery::mock(OnepayAdapter::class);

        $this->mockAdapter->shouldReceive('setCredentials')
            ->andReturn(['foo' => 'bar']);

        $this->onepay = new Onepay($this->mockTransbank);

        $this->onepay->setAdapter($this->mockAdapter);
    }

    public function testGetTransaction()
    {
        $this->mockTransbank->shouldReceive('isProduction')->once()
            ->andReturnTrue();

        $this->mockAdapter->shouldReceive('retrieveAndConfirm')
            ->andReturn(['foo' => 'bar']);

        $transaction = $this->onepay->getTransaction(['mock.transaction']);

        $this->assertInstanceOf(OnepayResponse::class, $transaction);
        $this->assertEquals('bar', $transaction->foo);

        $this->mockTransbank->shouldReceive('isProduction')->once()
            ->andReturnFalse();

        $transaction = $this->onepay->getTransaction(['mock.transaction']);

        $this->assertInstanceOf(OnepayResponse::class, $transaction);
        $this->assertEquals('bar', $transaction->foo);
    }
}
