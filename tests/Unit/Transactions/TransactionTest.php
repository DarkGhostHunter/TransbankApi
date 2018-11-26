<?php

namespace Tests\Unit\Transactions;

use PHPUnit\Framework\TestCase;
use DarkGhostHunter\TransbankApi\AbstractService;
use DarkGhostHunter\TransbankApi\Transactions\AbstractServiceTransaction;
use DarkGhostHunter\TransbankApi\Transbank;

class TransactionTest extends TestCase
{
    protected $mockService;

    protected $mockDefaults = [
        'default' => 'value'
    ];

    protected $mockAttributes = [
        'attribute' => 'value',
        'array' => [
            'array'
        ],
        'object' => null
    ];

    protected $mockCredentials = [
        'credential' => 'value'
    ];

    /** @var \Mockery\MockInterface|AbstractServiceTransaction */
    protected $transaction;

    protected function setUp()
    {
        $this->mockAttributes['object'] = new \stdClass();

        $this->transaction = new class extends AbstractServiceTransaction {};

        $transbank = \Mockery::mock(Transbank::class);
        $transbank->expects('getDefaults')
            ->once()
            ->andReturn($this->mockDefaults);
        $transbank->expects('getCredentials')
            ->once()
            ->andReturn($this->mockCredentials);

        $this->mockService = \Mockery::mock(AbstractService::class, [$transbank,
            'boot' => null
        ]);
    }

    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(AbstractServiceTransaction::class, $this->transaction);
    }

    public function testDoesNotInstantiatesWithoutArray()
    {
        $this->markTestSkipped();
    }

    public function testStaticInstancesFromJson()
    {
        $this->markTestSkipped();
    }

    public function testStaticDoesNotInstancesFromInvalidJson()
    {
        $this->markTestSkipped();
    }

    public function testSetAndGetService()
    {
        $this->transaction->setService($this->mockService);
        $this->assertInstanceOf(AbstractService::class, $this->transaction->getService());
    }

    public function testSetAndGetDefaults()
    {
        $this->transaction->setDefaults($this->mockDefaults);
        $this->assertEquals($this->mockDefaults, $this->transaction->getAttributes(),
            '', 0.0, 10, true);
    }

    public function testGetAndSetAttributes()
    {
        $this->markTestSkipped();
    }

    public function testGetAndSetType()
    {
        $this->markTestSkipped();
    }

    public function testInstantiatesWithAttributes()
    {
        $this->transaction = new $this->transaction($this->mockAttributes);

        $this->assertEquals($this->mockAttributes, $this->transaction->getAttributes(),
            '', 0.0, 10, true);
    }

    public function testMergesDefaults()
    {

        $this->transaction->setAttributes($this->mockAttributes);
        $this->transaction->setDefaults($defaults = [
            'attribute' => 'default',
            'newAttribute' => true,
        ]);

        $this->assertEquals(array_merge($defaults, $this->mockAttributes),
            $this->transaction->getAttributes(),
            '', 0.0, 10, true);
    }

    public function testBecomesArray()
    {
        $this->transaction = new $this->transaction($this->mockAttributes);

        $this->assertTrue(is_array($this->transaction->toArray()));
        $this->assertTrue(is_array((array)$this->transaction));
    }

    public function testBecomesJson()
    {
        $this->transaction = new $this->transaction($this->mockAttributes);

        $this->assertTrue(is_string($this->transaction->toJson()));
        $this->assertJson($this->transaction->toJson());
    }

    public function testBecomesString()
    {
        $this->transaction = new $this->transaction($this->mockAttributes);

        $this->assertTrue(is_string((string)$this->transaction));
    }

    public function testHidesAttributesFromSerialization()
    {
        $this->markTestSkipped();
    }

    public function testCanSetAndGetAttributeAsProperty()
    {
        $this->markTestSkipped();
    }

    public function testCanSetAndGetAttributeAsArray()
    {
        $this->markTestSkipped();
    }

    public function testCanUnsetAttributeAsArray()
    {
        $this->markTestSkipped();
    }

    public function testGetResult()
    {
        $this->markTestSkipped();
    }

    public function testDoesNotGetResultIfWasPerformed()
    {
        $this->markTestSkipped();
    }

    public function testForceGetResultEvenWhenAlreadyPerformed()
    {
        $this->markTestSkipped();
    }
}