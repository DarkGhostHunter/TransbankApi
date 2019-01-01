<?php

namespace Tests\Unit\Clients\Webpay;

use DarkGhostHunter\TransbankApi\Clients\Webpay\PlusComplete;
use DarkGhostHunter\TransbankApi\Clients\Webpay\SoapImplementation;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\ErrorResponseException;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\InvalidSignatureException;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use LuisUrrutia\TransbankSoap\Validation;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PlusCompleteTest extends TestCase
{
    /** @var PlusComplete */
    protected $client;

    /** @var SoapImplementation&\Mockery\MockInterface */
    protected $mockConnector;

    /** @var Validation&\Mockery\MockInterface */
    protected $mockValidator;

    protected function setUp()
    {
        $this->client = new PlusComplete(true, new Fluent(['privateKey' => 'foo', 'publicCert' => 'bar']));

        $this->client->boot();

        $this->mockConnector = \Mockery::instanceMock(SoapImplementation::class);

        $this->mockValidator = \Mockery::instanceMock('overload:' . Validation::class)
            ->makePartial();

        $this->client->setConnector($this->mockConnector);
    }

    public function testComplete()
    {
        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('initCompleteTransaction')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $response = $this->client->complete($transaction);

        $this->assertEquals('bar', $response['foo']);
    }

    public function testExceptionOnComplete()
    {
        $this->expectException(ErrorResponseException::class);

        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('initCompleteTransaction')
            ->with(\Mockery::type('array'))
            ->andThrow(\Exception::class);

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->complete($transaction);
    }

    public function testExceptionOnCompleteNotValid()
    {
        $this->expectException(InvalidSignatureException::class);

        $this->mockValidator->expects('isValid')->andReturnFalse();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('initCompleteTransaction')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->complete($transaction);
    }

    public function testQueryShare()
    {
        $this->mockValidator->expects('isValid')->andReturnTrue();

        $this->mockConnector
            ->expects('queryShare')
            ->with(\Mockery::type('object'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $response = $this->client->queryShare('test-token', 'test-order', 1);

        $this->assertEquals('bar', $response['foo']);
    }

    public function testExceptionOnQueryShare()
    {
        $this->expectException(ErrorResponseException::class);

        $this->mockValidator->expects('isValid')->andReturnTrue();

        $this->mockConnector
            ->expects('queryShare')
            ->with(\Mockery::type('object'))
            ->andThrow(\Exception::class);

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->queryShare('test-token', 'test-order', 1);
    }

    public function testExceptionOnQueryShareNotValid()
    {
        $this->expectException(InvalidSignatureException::class);

        $this->mockValidator->expects('isValid')->andReturnFalse();

        $this->mockConnector
            ->expects('queryShare')
            ->with(\Mockery::type('object'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->queryShare('test-token', 'test-order', 1);
    }

    public function testCharge()
    {
        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('authorize')
            ->with(\Mockery::type('object'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $response = $this->client->charge($transaction);

        $this->assertEquals('bar', $response['foo']);
    }

    public function testExceptionOnCharge()
    {
        $this->expectException(ErrorResponseException::class);

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockValidator->expects('isValid')->andReturnTrue();

        $this->mockConnector
            ->expects('authorize')
            ->with(\Mockery::type('object'))
            ->andThrow(\Exception::class);

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->charge($transaction);
    }

    public function testExceptionOnChargeNotValid()
    {
        $this->expectException(InvalidSignatureException::class);

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockValidator->expects('isValid')->andReturnFalse();

        $this->mockConnector
            ->expects('authorize')
            ->with(\Mockery::type('object'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->charge($transaction);
    }

    public function testConfirm()
    {
        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('acknowledgeCompleteTransaction')
            ->with(\Mockery::type('object'))
            ->andReturn(
                (object)['return' => true]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $response = $this->client->confirm($transaction);

        $this->assertTrue($response);
    }

    public function testExceptionOnConfirm()
    {
        $this->expectException(ErrorResponseException::class);

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockValidator->expects('isValid')->andReturnTrue();

        $this->mockConnector
            ->expects('acknowledgeCompleteTransaction')
            ->with(\Mockery::type('object'))
            ->andThrow(\Exception::class);

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->confirm($transaction);
    }

    public function testExceptionOnConfirmNotValid()
    {
        $this->expectException(InvalidSignatureException::class);

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockValidator->expects('isValid')->andReturnFalse();

        $this->mockConnector
            ->expects('acknowledgeCompleteTransaction')
            ->with(\Mockery::type('object'))
            ->andReturn(
                (object)['return' => true]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->confirm($transaction);
    }
}
