<?php

namespace Tests\Unit\Clients\Webpay;

use DarkGhostHunter\Fluid\Fluid;
use DarkGhostHunter\TransbankApi\Clients\Webpay\OneclickNormal;
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
class OneclickNormalTest extends TestCase
{
    /** @var OneclickNormal */
    protected $client;

    /** @var SoapImplementation&\Mockery\MockInterface */
    protected $mockConnector;

    /** @var Validation&\Mockery\MockInterface */
    protected $mockValidator;

    protected function setUp()
    {
        $this->client = new OneclickNormal(true, new Fluid(['privateKey' => 'foo', 'publicCert' => 'bar']));

        $this->client->boot();

        $this->mockConnector = \Mockery::instanceMock(SoapImplementation::class);

        $this->mockValidator = \Mockery::instanceMock('overload:' . Validation::class)
            ->makePartial();

        $this->client->setConnector($this->mockConnector);
    }

    public function testRegister()
    {
        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('initInscription')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $response = $this->client->register($transaction);

        $this->assertEquals('bar', $response['foo']);
    }

    public function testExceptionOnRegister()
    {
        $this->expectException(ErrorResponseException::class);

        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('initInscription')
            ->with(\Mockery::type('array'))
            ->andThrow(\Exception::class);

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->register($transaction);
    }

    public function testExceptionOnRegisterInvalid()
    {
        $this->expectException(InvalidSignatureException::class);

        $this->mockValidator->expects('isValid')->andReturnFalse();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('initInscription')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->register($transaction);
    }

    public function testConfirm()
    {
        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('finishInscription')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $response = $this->client->confirm($transaction);

        $this->assertEquals('bar', $response['foo']);
    }

    public function testExceptionOnConfirm()
    {
        $this->expectException(ErrorResponseException::class);

        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('finishInscription')
            ->with(\Mockery::type('array'))
            ->andThrow(\Exception::class);

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->confirm($transaction);
    }

    public function testExceptionOnConfirmInvalid()
    {
        $this->expectException(InvalidSignatureException::class);

        $this->mockValidator->expects('isValid')->andReturnFalse();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('finishInscription')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->confirm($transaction);
    }

    public function testUnregister()
    {
        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('removeUser')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $unregistration = $this->client->unregister($transaction);

        $this->assertEquals('bar', $unregistration['foo']);
    }

    public function testExceptionOnUnregister()
    {
        $this->expectException(ErrorResponseException::class);

        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('removeUser')
            ->with(\Mockery::type('array'))
            ->andThrow(\Exception::class);

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->unregister($transaction);
    }

    public function testExceptionOnUnregisterInvalid()
    {
        $this->expectException(InvalidSignatureException::class);

        $this->mockValidator->expects('isValid')->andReturnFalse();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('removeUser')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->unregister($transaction);
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
            ->with(\Mockery::type('array'))
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

        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('authorize')
            ->with(\Mockery::type('array'))
            ->andThrow(\Exception::class);

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->charge($transaction);
    }

    public function testExceptionOnChargeInvalid()
    {
        $this->expectException(InvalidSignatureException::class);

        $this->mockValidator->expects('isValid')->andReturnFalse();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('authorize')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->charge($transaction);
    }

    public function testReverse()
    {
        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('codeReverseOneClick')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $response = $this->client->reverse($transaction);

        $this->assertEquals('bar', $response['foo']);
    }

    public function testExceptionOnReverse()
    {
        $this->expectException(ErrorResponseException::class);

        $this->mockValidator->expects('isValid')->andReturnTrue();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('codeReverseOneClick')
            ->with(\Mockery::type('array'))
            ->andThrow(\Exception::class);

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->reverse($transaction);
    }

    public function testExceptionOnReverseInvalid()
    {
        $this->expectException(InvalidSignatureException::class);

        $this->mockValidator->expects('isValid')->andReturnFalse();

        $transaction = new WebpayTransaction([
            'buyOrder' => 'baz',
            'amount' => 'qux',
        ]);

        $this->mockConnector
            ->expects('codeReverseOneClick')
            ->with(\Mockery::type('array'))
            ->andReturn(
                (object)['return' => ['foo' => 'bar']]
            );

        $this->mockConnector
            ->expects('__getLastResponse')
            ->andReturn('foo');

        $this->client->reverse($transaction);
    }
}
