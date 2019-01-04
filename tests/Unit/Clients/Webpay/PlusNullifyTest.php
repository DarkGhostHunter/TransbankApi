<?php

namespace Tests\Unit\Clients\Webpay;

use DarkGhostHunter\Fluid\Fluid;
use DarkGhostHunter\TransbankApi\Clients\Webpay\PlusNullify;
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
class PlusNullifyTest extends TestCase
{
    /** @var PlusNullify */
    protected $client;

    /** @var SoapImplementation&\Mockery\MockInterface */
    protected $mockConnector;

    /** @var Validation&\Mockery\MockInterface */
    protected $mockValidator;

    protected function setUp()
    {
        $this->client = new PlusNullify(true, new Fluid(['privateKey' => 'foo', 'publicCert' => 'bar']));

        $this->client->boot();

        $this->mockConnector = \Mockery::instanceMock(SoapImplementation::class);

        $this->mockValidator = \Mockery::instanceMock('overload:' . Validation::class)
            ->makePartial();

        $this->client->setConnector($this->mockConnector);
    }

    public function testNullify()
    {
        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('nullify')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $response = $this->client->nullify($transaction);

        $this->assertEquals('bar', $response['foo']);
    }

    public function testExceptionNullify()
    {
        $this->expectException(ErrorResponseException::class);

        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('nullify')
            ->with(\Mockery::type('array'))
            ->andThrow(\Exception::class);

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $response = $this->client->nullify($transaction);

        $this->assertEquals('bar', $response['foo']);
    }

    public function testExceptionNullifyInvalid()
    {
        $this->expectException(InvalidSignatureException::class);

        $this->mockValidator->expects('isValid')->andReturnFalse();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('nullify')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->nullify($transaction);
    }
}
