<?php

namespace Tests\Unit\Transactions;

use DarkGhostHunter\TransbankApi\AbstractService;
use DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction;
use PHPUnit\Framework\TestCase;

class AbstractTransactionTest extends TestCase
{

    /** @var AbstractService&\Mockery\MockInterface */
    protected $mockService;

    /** @var AbstractTransaction */
    protected $transaction;

    protected function setUp()
    {
        $this->mockService = \Mockery::mock(AbstractService::class);

        $this->transaction = new class extends AbstractTransaction {};

    }


    public function testGetAndSetService()
    {
        $this->transaction->setService($this->mockService);

        $this->assertInstanceOf(AbstractService::class, $this->transaction->getService());
    }

    public function testGetAndSetType()
    {
        $this->transaction->setType('test-type');

        $this->assertEquals('test-type', $this->transaction->getType());
    }

    public function testCommit()
    {
        $this->mockService->shouldReceive('commit')->once()
            ->with($this->transaction)
            ->andReturn($array = ['foo' => 'bar']);

        $this->transaction->setService($this->mockService);

        $response = $this->transaction->commit();

        $this->assertEquals($array, $response);
    }

    public function testSingleCommit()
    {
        $this->mockService->shouldReceive('commit')->once()
            ->with($this->transaction)
            ->andReturn($array = ['foo' => 'bar']);

        $this->transaction->setService($this->mockService);

        $this->transaction->commit();

        $response = $this->transaction->commit();

        $this->assertEquals($array, $response);
    }

    public function testForceCommit()
    {
        $this->mockService->shouldReceive('commit')->twice()
            ->with($this->transaction)
            ->andReturn($array = ['foo' => 'bar']);

        $this->transaction->setService($this->mockService);

        $response = $this->transaction->commit();

        $forcedResponse = $this->transaction->forceCommit();

        $this->assertEquals($array, $response);
        $this->assertEquals($array, $forcedResponse);
    }

    public function testSetDefaults()
    {
        $this->transaction->baz = 'qux';
        $this->transaction->quux = 'quuz';

        $this->transaction->setDefaults([
            'foo' => 'bar',
            'quux' => 'notQuuz'
        ]);

        $this->assertEquals('bar', $this->transaction->foo);
        $this->assertEquals('qux', $this->transaction->baz);
        $this->assertEquals('quuz', $this->transaction->quux);
    }
}
