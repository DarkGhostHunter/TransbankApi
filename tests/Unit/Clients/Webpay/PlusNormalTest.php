<?php

namespace Tests\Unit\Clients\Webpay;

use DarkGhostHunter\Fluid\Fluid;
use DarkGhostHunter\TransbankApi\Clients\Webpay\PlusNormal;
use DarkGhostHunter\TransbankApi\Clients\Webpay\SoapImplementation;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\InvalidSignatureException;
use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use LuisUrrutia\TransbankSoap\Validation;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PlusNormalTest extends TestCase
{

    /** @var PlusNormal */
    protected $client;

    /** @var SoapImplementation&\Mockery\MockInterface */
    protected $mockConnector;

    /** @var Validation&\Mockery\MockInterface */
    protected $mockValidator;

    protected function setUp()
    {
        $this->client = new PlusNormal(true, new Fluid(['privateKey' => 'foo', 'publicCert' => 'bar']));

        $this->client->boot();

        $this->mockConnector = \Mockery::instanceMock(SoapImplementation::class);

        $this->mockValidator = \Mockery::instanceMock('overload:' . Validation::class)
            ->makePartial();

        $this->client->setConnector($this->mockConnector);
    }

    public function testRetrieveAndConfirm()
    {
        $this->mockValidator->expects('isValid')->andReturnTrue();

        $this->mockConnector
            ->expects('getTransactionResult')
            ->with(\Mockery::type('object'))
            ->andReturnUsing(function (Fluid $fluent) {
                $this->assertEquals('test-confirm', $fluent->tokenInput);
                return (object)['return' => ['foo' => 'bar']];
            });

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->mockConnector
            ->expects('acknowledgeTransaction')
            ->with(\Mockery::type('object'))
            ->andReturnTrue();

        $response = $this->client->retrieveAndConfirm('test-confirm');

        $this->assertEquals('bar', $response['foo']);
    }

    public function testExceptionOnRetrieveAndConfirm()
    {
        $this->expectException(InvalidSignatureException::class);

        $this->mockValidator->expects('isValid')->andReturnFalse();

        $this->mockConnector
            ->expects('getTransactionResult')
            ->with(\Mockery::type('object'))
            ->andReturnUsing(function (Fluid $fluent) {
                $this->assertEquals('test-confirm', $fluent->tokenInput);
                return (object)['return' => ['foo' => 'bar']];
            });

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->mockConnector
            ->expects('acknowledgeTransaction')
            ->with(\Mockery::type('object'))
            ->andReturnTrue();

        $response = $this->client->retrieveAndConfirm('test-confirm');

        $this->assertEquals('bar', $response['foo']);
    }

    public function testCommitNormal()
    {
        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('initTransaction')
            ->with(\Mockery::type('array'))
            ->andReturnUsing(function ($array) {
                $this->assertEquals('TR_NORMAL_WS', $array['wsInitTransactionInput']->wSTransactionType);
                return (object)['return' => ['foo' => 'bar']];
            });

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $response = $this->client->commit($transaction);

        $this->assertEquals('bar', $response['foo']);
    }

    public function testCommitMallNormal()
    {
        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
            'items' => [
                [
                    'commerceCode' => 'test-code',
                    'buyOrder' => 'test-order',
                    'amount' => 9999,
                ]
            ]
        ]);

        $this->mockConnector
            ->expects('initTransaction')
            ->with(\Mockery::type('array'))
            ->andReturnUsing(function ($array) {
                $this->assertEquals('TR_MALL_WS', $array['wsInitTransactionInput']->wSTransactionType);
                return (object)['return' => ['foo' => 'bar']];
            });

        $this->mockConnector
            ->expects('__getLastResponse');

        $response = $this->client->commit($transaction);

        $this->assertEquals('bar', $response['foo']);
    }

    public function testExceptionOnCommit()
    {
        $this->expectException(ErrorResponseException::class);

        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('initTransaction')
            ->with(\Mockery::type('array'))
            ->andThrow(\Exception::class);

        $this->client->commit($transaction);
    }

    public function testExceptionOnCommitInvalid()
    {
        $this->expectException(InvalidSignatureException::class);

        $this->mockValidator->expects('isValid')->andReturnFalse();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('initTransaction')
            ->with(\Mockery::type('array'))
            ->andReturnUsing(function ($array) {
                $this->assertEquals('TR_NORMAL_WS', $array['wsInitTransactionInput']->wSTransactionType);
                return (object)['return' => ['foo' => 'bar']];
            });

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->commit($transaction);
    }
}
