<?php


namespace Tests\Unit\TransactionFactories;

use PHPUnit\Framework\TestCase;
use DarkGhostHunter\TransbankApi\Responses\WebpayOneclickResponse;
use DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse;
use DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse;
use DarkGhostHunter\TransbankApi\Adapters\WebpaySoapAdapter;
use DarkGhostHunter\TransbankApi\Contracts\AdapterInterface;
use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use DarkGhostHunter\TransbankApi\Transactions\WebpayMallTransaction;
use DarkGhostHunter\TransbankApi\Transbank;
use DarkGhostHunter\TransbankApi\Webpay;

class WebpayTransactionsTest extends TestCase
{
    /** @var Webpay */
    protected $webpayIntegration;

    /** @var Webpay */
    protected $webpayProduction;

    /** @var \Mockery\MockInterface|WebpaySoapAdapter */
    protected $mockAdapter;

    protected $mockCredentials = [
        'commerceCode' => '5000000001',
        'publicKey' => 'ABCD1234EF...',
        'publicCert' => '---BEGIN CERTIFICATE---...'
    ];

    protected $mockDefaults = [
        'plusReturnUrl' => 'http://app.com/webpay/normal/result',
        'plusFinalUrl' => 'http://app.com/webpay/normal/receipt',
        'plusMallReturnUrl' => 'http://app.com/webpay/mall/result',
        'plusMallFinalUrl' => 'http://app.com/webpay/mall/receipt',
        'oneclickReturnUrl' => 'http://app.com/webpay/oneclick/result',
    ];

    protected $mockTransactionAttributes = [
        'sessionId' => 'client-session-id-88',
        'buyOrder'  => 'myOrder#16548',
        'amount'    => 1000,
        'returnUrl' => 'https://app.com/transaction/result',
        'finalUrl'  => 'https://app.com/transaction/receipt',
    ];

    protected function setUp()
    {
        $this->mockAdapter = \Mockery::instanceMock(WebpaySoapAdapter::class, AdapterInterface::class);
    }

    protected function setWebpay()
    {
        $transbankIntegration = Transbank::environment();
        $transbankProduction = Transbank::environment('production', [
            'webpay' => $this->mockCredentials
        ]);
        $transbankIntegration->setDefaults('webpay', $this->mockDefaults);
        $transbankProduction->setDefaults('webpay', $this->mockDefaults);

        $this->webpayIntegration = Webpay::fromConfig($transbankIntegration);
        $this->webpayProduction = Webpay::fromConfig($transbankProduction);

        $this->webpayIntegration->setAdapter($this->mockAdapter);
        $this->webpayProduction->setAdapter($this->mockAdapter);
    }

    protected function adapterExpectsCredentials()
    {
        $this->mockAdapter->shouldReceive('setCredentials')
            ->once()
            ->with(\Mockery::type('array'))
            ->andReturnUndefined();
    }

    protected function adapterExpectsCommitNormal()
    {
        $this->mockAdapter->shouldReceive('commit')
            ->with(\Mockery::type(WebpayTransaction::class))
            ->andReturn([
                'type' => 'normal',
                'test' => 'ok'
            ]);
    }

    protected function adapterExpectsCommitMall()
    {
        $this->mockAdapter->shouldReceive('commit')
            ->with(\Mockery::type(WebpayMallTransaction::class))
            ->andReturn([
                'type' => 'mall',
                'test' => 'ok'
            ]);
    }

    protected function assertTransaction($transaction, $class, $type)
    {
        $this->assertInstanceOf($class, $transaction);
        $this->assertTrue($transaction->getType() === $type);
        $this->assertEquals(
            $this->mockTransactionAttributes,
            $transaction->getAttributes(),
            '', 0.0, 10, true
        );
    }

    public function testMakesNormal()
    {
        $this->setWebpay();

        $transaction = $this->webpayIntegration->makeNormal($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'plus.normal');

        $transaction = $this->webpayProduction->makeNormal($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'plus.normal');
    }

    public function testCreatesNormal()
    {
        $this->adapterExpectsCredentials();
        $this->adapterExpectsCommitNormal();
        $this->setWebpay();

        $results = $this->webpayIntegration->createNormal($this->mockTransactionAttributes);

        var_dump($results);

        $this->assertInstanceOf(WebpayPlusResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));

        $results = $this->webpayProduction->createNormal($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayPlusResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));
    }

    public function testMakesMallNormal()
    {
        $this->adapterExpectsCommitMall();
        $this->setWebpay();

        $transaction = $this->webpayIntegration->makeMallNormal($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayMallTransaction::class, 'plus.mall.normal');

        $transaction = $this->webpayProduction->makeMallNormal($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayMallTransaction::class, 'plus.mall.normal');
    }

    public function testCreatesMallNormal()
    {
        $this->adapterExpectsCredentials();
        $this->adapterExpectsCommitMall();
        $this->setWebpay();

        $results = $this->webpayIntegration->createMallNormal($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayPlusMallResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));

        $results = $this->webpayProduction->createMallNormal($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayPlusMallResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));
    }

    public function testMakesNullify()
    {
        $this->setWebpay();

        $transaction = $this->webpayIntegration->makeNullify($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'plus.nullify');

        $transaction = $this->webpayProduction->makeNullify($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'plus.nullify');
    }

    public function testCreatesNullify()
    {
        $this->adapterExpectsCredentials();
        $this->adapterExpectsCommitNormal();
        $this->setWebpay();

        $results = $this->webpayIntegration->createNullify($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayPlusResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));

        $results = $this->webpayProduction->createNullify($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayPlusResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));
    }

    public function testMakesRegistration()
    {
        $this->setWebpay();

        $transaction = $this->webpayIntegration->makeRegistration($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'oneclick.register');

        $transaction = $this->webpayProduction->makeRegistration($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'oneclick.register');
    }

    public function testCreatesRegistration()
    {
        $this->adapterExpectsCredentials();
        $this->adapterExpectsCommitNormal();
        $this->setWebpay();

        $results = $this->webpayIntegration->createRegistration($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));

        $results = $this->webpayProduction->createRegistration($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));
    }

    public function testMakesUnregistration()
    {
        $this->setWebpay();

        $transaction = $this->webpayIntegration->makeUnregistration($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'oneclick.unregister');

        $transaction = $this->webpayProduction->makeUnregistration($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'oneclick.unregister');
    }

    public function testCreatesUnregistration()
    {
        $this->adapterExpectsCredentials();
        $this->adapterExpectsCommitNormal();
        $this->setWebpay();

        $results = $this->webpayIntegration->createUnregistration($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));

        $results = $this->webpayProduction->createUnregistration($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));
    }

    public function testMakesCharge()
    {
        $this->setWebpay();

        $transaction = $this->webpayIntegration->makeCharge($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'oneclick.charge');

        $transaction = $this->webpayProduction->makeCharge($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'oneclick.charge');
    }

    public function testCreatesCharge()
    {
        $this->adapterExpectsCredentials();
        $this->adapterExpectsCommitNormal();
        $this->setWebpay();

        $results = $this->webpayIntegration->createCharge($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));

        $results = $this->webpayProduction->createCharge($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));
    }

    public function testMakesReverseCharge()
    {
        $this->setWebpay();

        $transaction = $this->webpayIntegration->makeReverseCharge($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'oneclick.reverse');

        $transaction = $this->webpayProduction->makeReverseCharge($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'oneclick.reverse');
    }

    public function testCreatesReverseCharge()
    {
        $this->adapterExpectsCredentials();
        $this->adapterExpectsCommitNormal();
        $this->setWebpay();

        $results = $this->webpayIntegration->createReverseCharge($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));

        $results = $this->webpayProduction->createReverseCharge($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));
    }

    public function testMakesMallCharge()
    {
        $this->adapterExpectsCommitMall();
        $this->setWebpay();

        $transaction = $this->webpayIntegration->makeMallCharge($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayMallTransaction::class, 'oneclick.mall.charge');

        $transaction = $this->webpayProduction->makeMallCharge($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayMallTransaction::class, 'oneclick.mall.charge');
    }

    public function testCreatesMallCharge()
    {
        $this->adapterExpectsCredentials();
        $this->adapterExpectsCommitMall();
        $this->setWebpay();

        $results = $this->webpayIntegration->createMallCharge($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));

        $results = $this->webpayProduction->createMallCharge($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));
    }

    public function testMakesMallReverse()
    {
        $this->adapterExpectsCommitMall();
        $this->setWebpay();

        $transaction = $this->webpayIntegration->makeMallReverseCharge($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayMallTransaction::class, 'oneclick.mall.reverse');

        $transaction = $this->webpayProduction->makeMallReverseCharge($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayMallTransaction::class, 'oneclick.mall.reverse');
    }

    public function testCreatesMallReverse()
    {
        $this->adapterExpectsCredentials();
        $this->adapterExpectsCommitMall();
        $this->setWebpay();

        $results = $this->webpayIntegration->createMallReverseCharge($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));

        $results = $this->webpayProduction->createMallReverseCharge($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));
    }

    public function testMakesMallNullify()
    {
        $this->setWebpay();

        $transaction = $this->webpayIntegration->makeMallNullify($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'oneclick.mall.nullify');

        $transaction = $this->webpayProduction->makeMallNullify($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'oneclick.mall.nullify');
    }

    public function testCreatesMallNullify()
    {
        $this->adapterExpectsCredentials();
        $this->adapterExpectsCommitNormal();
        $this->setWebpay();

        $results = $this->webpayIntegration->createMallNullify($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));

        $results = $this->webpayProduction->createMallNullify($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));
    }

    public function testMakesReverseNullify()
    {
        $this->setWebpay();

        $transaction = $this->webpayIntegration->makeMallReverseNullify($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'oneclick.mall.reverseNullify');

        $transaction = $this->webpayProduction->makeMallReverseNullify($this->mockTransactionAttributes);

        $this->assertTransaction($transaction, WebpayTransaction::class, 'oneclick.mall.reverseNullify');
    }

    public function testCreatesReverseNullify()
    {
        $this->adapterExpectsCredentials();
        $this->adapterExpectsCommitNormal();
        $this->setWebpay();

        $results = $this->webpayIntegration->createMallReverseNullify($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));

        $results = $this->webpayProduction->createMallReverseNullify($this->mockTransactionAttributes);

        $this->assertInstanceOf(WebpayOneclickResponse::class, $results);
        $this->assertEquals('ok', $results->get('test'));
    }

}