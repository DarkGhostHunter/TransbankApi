<?php

namespace Tests\Unit\TransactionFactories;

use DarkGhostHunter\Fluid\Fluid;
use DarkGhostHunter\TransbankApi\Adapters\OnepayAdapter;
use DarkGhostHunter\TransbankApi\Onepay;
use DarkGhostHunter\TransbankApi\Responses\OnepayResponse;
use DarkGhostHunter\TransbankApi\Transactions\OnepayNullifyTransaction;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;
use DarkGhostHunter\TransbankApi\Transbank;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class OnepayTransactionFactoryTest extends TestCase
{

    /** @var Transbank&\Mockery\MockInterface */
    protected $mockTransbank;

    /** @var Onepay */
    protected $onepay;

    /** @var OnepayAdapter&\Mockery\MockInterface */
    protected $mockAdapter;

    protected function setUp() : void
    {
        $this->mockTransbank = \Mockery::mock(Transbank::class);
        $this->mockTransbank->shouldReceive('getDefaults')->once()
            ->andReturn(['foo' => 'bar']);
        $this->mockTransbank->shouldReceive('getCredentials')->once()
            ->with('onepay')
            ->andReturn(new Fluid(['foo' => 'bar']));
        $this->mockTransbank->shouldReceive('isProduction')->once()
            ->andReturnTrue();
        $this->mockTransbank->shouldReceive('getEnvironment')
            ->andReturn('production');

        $this->mockAdapter = \Mockery::mock(OnepayAdapter::class);

        $this->onepay = new Onepay($this->mockTransbank, new NullLogger());
        $this->onepay->setAdapter($this->mockAdapter);
    }

    public function testCreateNullify()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once()
            ->andReturn(['foo' => 'bar']);
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                $this->assertInstanceOf(OnepayNullifyTransaction::class, $transaction);
                return $transaction->toArray();
            });

        $response = $this->onepay->createNullify([
            'foo' => 'bar',
            'amount' => 1,
            'quantity' => 1,
        ]);

        $this->assertInstanceOf(OnepayResponse::class, $response);
        $this->assertEquals('bar', $response->foo);
        $this->assertEquals(1, $response->amount);
        $this->assertEquals(1, $response->quantity);
    }

    public function testMakeCart()
    {
        $cart = $this->onepay->makeCart([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(OnepayTransaction::class, $cart);
        $this->assertEquals('bar', $cart->foo);
    }

    public function testCreateCart()
    {
        $this->mockAdapter->shouldReceive('setCredentials')->once()
            ->andReturn(['foo' => 'bar']);
        $this->mockAdapter->shouldReceive('commit')->once()
            ->andReturnUsing(function ($transaction) {
                $this->assertInstanceOf(OnepayTransaction::class, $transaction);
                return $transaction->items[0]->toArray();
            });

        $response = $this->onepay->createCart([
            'items' => [
                'foo' => 'bar',
                'amount' => 1,
                'quantity' => 1,
            ]
        ]);

        $this->assertInstanceOf(OnepayResponse::class, $response);
        $this->assertEquals('bar', $response->foo);
        $this->assertEquals(1, $response->amount);
        $this->assertEquals(1, $response->quantity);
    }

    public function testMakeNullify()
    {
        $nullify = $this->onepay->makeNullify([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(OnepayNullifyTransaction::class, $nullify);
        $this->assertEquals('bar', $nullify->foo);
    }
}
