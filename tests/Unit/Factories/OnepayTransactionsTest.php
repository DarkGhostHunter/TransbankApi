<?php


namespace Tests\Unit\Factories;

use DarkGhostHunter\TransbankApi\Responses\OnepayResponse;
use PHPUnit\Framework\TestCase;
use DarkGhostHunter\TransbankApi\Adapters\OnepayAdapter;
use DarkGhostHunter\TransbankApi\Adapters\WebpaySoapAdapter;
use DarkGhostHunter\TransbankApi\Contracts\AdapterInterface;
use DarkGhostHunter\TransbankApi\Onepay;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;
use DarkGhostHunter\TransbankApi\Transbank;

class OnepayTransactionsTest extends TestCase
{
    /** @var Onepay */
    protected $onepayIntegration;

    /** @var Onepay */
    protected $onepayProduction;

    /** @var \Mockery\MockInterface|WebpaySoapAdapter */
    protected $mockAdapter;

    protected $mockCredentials = [
        'apiKey' => 'ABCD1234EF...',
        'secret' => 'ABCD1234EF...',
    ];

    protected $mockDefaults = [
        'channel' => 'web',
        'qrCode' => true,
        'appScheme' => 'my-app://app//onepay-result',
        'callbackUrl' => 'https://app.com/onepay/result',
    ];

    protected $mockCart = [
        'sessionId' => 'app-session-id#654687',
        'buyOrder' => 'app-order#67987',
        'items' => [
            [
                'description' => 'Producto de Prueba',
                'quantity' => 1,
                'amount' => 9990,
            ],
            [
                'description' => 'Producto de Prueba 2',
                'quantity' => 3,
                'amount' => 4990,
            ],
        ]
    ];

    protected $total = 9990 + 4999;

    protected function setUp()
    {
        $this->mockAdapter = \Mockery::instanceMock(OnepayAdapter::class, AdapterInterface::class);
    }

    protected function setOnepay()
    {
        $transbankIntegration = Transbank::environment();
        $transbankProduction = Transbank::environment('production', [
            'onepay' => $this->mockCredentials
        ]);
        $transbankIntegration->setDefaults('onepay', $this->mockDefaults);
        $transbankProduction->setDefaults('onepay', $this->mockDefaults);

        $this->onepayIntegration = Onepay::fromConfig($transbankIntegration);
        $this->onepayProduction = Onepay::fromConfig($transbankProduction);

        $this->onepayIntegration->setAdapter($this->mockAdapter);
        $this->onepayProduction->setAdapter($this->mockAdapter);
    }

    protected function adapterExpectsCredentials()
    {
        $this->mockAdapter->shouldReceive('setCredentials')
            ->once()
            ->with(\Mockery::type('array'))
            ->andReturnUndefined();
    }

    protected function adapterExpectsCommit()
    {
        $this->mockAdapter->shouldReceive('commit')
            ->with(\Mockery::type(OnepayTransaction::class))
            ->andReturn([
                'test' => 'ok'
            ]);
    }

    protected function assertTransaction($transaction, $class, $type)
    {
        $this->assertInstanceOf($class, $transaction);
        $this->assertTrue($transaction->getType() === $type);

        $this->assertArrayHasKey('items', $transaction->toArray());
        $this->assertArrayHasKey('total', $transaction->toArray());
        $this->assertEquals($transaction->buyOrder, $this->mockCart['buyOrder']);
        $this->assertEquals($transaction->sessionId, $this->mockCart['sessionId']);
    }

    public function testMakesCart()
    {
        $this->setOnepay();

        $transaction = $this->onepayIntegration->makeCart($this->mockCart);

        $this->assertTransaction($transaction, OnepayTransaction::class, 'onepay.cart');

        $transaction = $this->onepayProduction->makeCart($this->mockCart);

        $this->assertTransaction($transaction, OnepayTransaction::class, 'onepay.cart');
    }

    public function testCreatesCart()
    {
        $this->adapterExpectsCredentials();
        $this->adapterExpectsCommit();
        $this->setOnepay();

        $results = $this->onepayIntegration->createCart($this->mockCart);

        $this->assertInstanceOf(OnepayResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));

        $results = $this->onepayProduction->createCart($this->mockCart);

        $this->assertInstanceOf(OnepayResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));
    }

}