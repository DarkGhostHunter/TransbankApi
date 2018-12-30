<?php

namespace Tests\Unit\Transactions;

use DarkGhostHunter\TransbankApi\Contracts;
use PHPUnit\Framework\TestCase;
use DarkGhostHunter\TransbankApi\AbstractService;
use DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction;
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

    /** @var \Mockery\MockInterface|AbstractTransaction */
    protected $transaction;

    protected function setUp()
    {
        $this->mockAttributes['object'] = new \stdClass();

        $this->transaction = new class extends AbstractTransaction
        {
        };

        $transbank = \Mockery::mock(Transbank::class);
        $transbank->expects('getDefaults')
            ->once()
            ->andReturn($this->mockDefaults);
        $transbank->expects('getCredentials')
            ->once()
            ->andReturn($this->mockCredentials);

//        $this->mockService = \Mockery::mock(AbstractService::class, [$transbank])
//            ->shouldAllowMockingProtectedMethods();

        $this->mockService = $this->createMockService($transbank);
    }

    protected function createMockService($transbank)
    {
        return new class($transbank) extends AbstractService
        {

            /**
             * Instantiates (and/or boots) the Adapter for the Service
             *
             * @return void
             */
            public function bootAdapter()
            {
                // TODO: Implement bootAdapter() method.
            }

            /**
             * Instantiates (and/or boots) the WebpayClient Factory for the Service
             *
             * @return void
             */
            public function bootTransactionFactory()
            {
                // TODO: Implement bootTransactionFactory() method.
            }

            /**
             * Get the Service Credentials for the Production Environment
             *
             * @return array
             */
            protected function getProductionCredentials()
            {
                // TODO: Implement getProductionCredentials() method.
            }

            /**
             * Get the Service Credentials for the Integration Environment
             *
             * @param string $type
             * @return array
             */
            protected function getIntegrationCredentials(string $type = null)
            {
                // TODO: Implement getIntegrationCredentials() method.
            }

            /**
             * Transform the adapter raw answer of a transaction commitment to a
             * more friendly Webpay Response
             *
             * @param array $result
             * @param string $type
             * @return Contracts\ResponseInterface
             */
            protected function parseResponse(array $result, string $type)
            {
                // TODO: Implement parseResponse() method.
            }
        };
    }

    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(AbstractTransaction::class, $this->transaction);
    }

    public function testReceivesArray()
    {
        $transaction = \Mockery::mock(AbstractTransaction::class, [
            ['foo' => 'bar']
        ])->makePartial();

        $this->assertEquals('bar', $transaction->foo);
    }

    public function testReceivesJson()
    {
        $transaction = $this->transaction::fromJson(json_encode(['foo' => 'bar']));

        $this->assertEquals('bar', $transaction->foo);
    }

    public function testStaticDoesNotInstancesFromInvalidJson()
    {
        $this->expectException(\TypeError::class);

        $json = '"{"foo"\:\\"bar"}/]"';
        $transaction = $this->transaction::fromJson($json);
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
        $this->transaction->setAttributes([
            'foo' => 'bar'
        ]);
        $this->transaction->setAttributes([
            'quax' => 'exort'
        ]);

        $this->assertEquals('exort', $this->transaction->get('quax'));
        $this->assertNull($this->transaction->get('foo'));

        $this->assertEquals(['quax' => 'exort'], $this->transaction->getAttributes());
    }

    public function testGetAndSetType()
    {
        $this->transaction->setType('foo');
        $this->assertEquals('foo', $this->transaction->getType());
    }

    public function testInstantiatesWithAttributes()
    {
        $this->transaction = new $this->transaction($this->mockAttributes);

        $this->assertEquals($this->mockAttributes, $this->transaction->getAttributes());
    }

    public function testMergesDefaults()
    {

        $this->transaction->setAttributes($this->mockAttributes);
        $this->transaction->setDefaults($defaults = [
            'attribute' => 'default',
            'newAttribute' => true,
        ]);

        $this->assertEquals(array_merge($defaults, $this->mockAttributes),
            $this->transaction->getAttributes());
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