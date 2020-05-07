<?php

namespace Tests\Unit\Transbank;

use DarkGhostHunter\Fluid\Fluid;
use DarkGhostHunter\TransbankApi\Exceptions\Credentials\CredentialInvalidException;
use DarkGhostHunter\TransbankApi\Exceptions\Transbank\InvalidServiceException;
use DarkGhostHunter\TransbankApi\Onepay;
use DarkGhostHunter\TransbankApi\Transbank;
use DarkGhostHunter\TransbankApi\Webpay;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class TransbankTest extends TestCase
{

    /** @var Transbank */
    protected $transbank;

    protected function setUp() : void
{
        $this->transbank = new Transbank(new NullLogger());
    }

    public function test__construct()
    {
        $transbank = new Transbank(new NullLogger());

        $this->assertInstanceOf(Transbank::class, $transbank);
    }

    public function testMake()
    {
        $transbank = Transbank::make();
        $this->assertInstanceOf(Transbank::class, $transbank);
        $this->assertInstanceOf(NullLogger::class, $transbank->getLogger());
        $this->assertTrue($transbank->isIntegration());
        $this->assertNull($transbank->getCredentials('webpay'));

        $transbank = Transbank::make('notProduction', ['webpay' => ['foo' => 'bar']]);
        $this->assertInstanceOf(Transbank::class, $transbank);
        $this->assertInstanceOf(NullLogger::class, $transbank->getLogger());
        $this->assertTrue($transbank->isIntegration());
        $this->assertEquals('bar', $transbank->getCredentials('webpay')->foo);

        $mockLogger = new class extends NullLogger {
            public function foo() { return 'bar'; }
        };

        $transbank = Transbank::make('production', ['onepay' => ['foo' => 'bar']], $mockLogger);
        $this->assertInstanceOf(Transbank::class, $transbank);
        $this->assertEquals('bar', $transbank->getLogger()->foo());
        $this->assertTrue($transbank->isProduction());
        $this->assertEquals('bar', $transbank->getCredentials('onepay')->foo);
    }

    public function testExceptionOnMakeWithInvalidService()
    {
        $this->expectException(InvalidServiceException::class);

        Transbank::make('notProduction', ['anyService' => ['foo' => 'bar']]);
    }

    public function testGetAndSetLogger()
    {
        $transbank = new Transbank(new NullLogger());

        $mockLogger = new class extends NullLogger {
            public function foo() { return 'bar'; }
        };

        $this->assertInstanceOf(NullLogger::class, $transbank->getLogger());

        $transbank->setLogger($mockLogger);
        $this->assertEquals('bar', $transbank->getLogger()->foo());
    }

    public function testSetAndGetDefaults()
    {
        $this->transbank->setDefaults('webpay', $array = ['foo' => 'bar']);

        $this->assertEquals($array, $this->transbank->getDefaults('webpay'));

        $this->transbank->setDefaults('onepay', $array = ['foo' => 'bar']);

        $this->assertEquals($array, $this->transbank->getDefaults('onepay'));
    }

    public function testExceptionOnInvalidServiceDefaults()
    {
        $this->expectException(InvalidServiceException::class);

        $this->transbank->setDefaults('anyService', ['foo' => 'bar']);
    }

    public function testSetAndGetCredentials()
    {
        $this->transbank->setCredentials('webpay', $array = ['foo' => 'bar']);

        $this->assertInstanceOf(Fluid::class, $this->transbank->getCredentials('webpay'));
        $this->assertEquals('bar', $this->transbank->getCredentials('webpay')->foo);

        $this->assertNull($this->transbank->getCredentials('anyService'));
    }

    public function testExceptionOnSetCredentialsForInvalidService()
    {
        $this->expectException(InvalidServiceException::class);
        $this->transbank->setCredentials('anyService', $array = ['foo' => 'bar']);
    }

    public function testExceptioOnSetInvalidCredentials()
    {
        $this->expectException(CredentialInvalidException::class);
        $this->transbank->setCredentials('webpay', ['foo' => (object)['lol']]);
    }

    public function testSetAndGetEnvironment()
    {
        $this->transbank->setEnvironment('integration');
        $this->assertEquals('integration', $this->transbank->getEnvironment());
        $this->transbank->setEnvironment('notProduction');
        $this->assertEquals('integration', $this->transbank->getEnvironment());
        $this->transbank->setEnvironment('production');
        $this->assertEquals('production', $this->transbank->getEnvironment());

    }

    public function testIsIntegrationAndIsProduction()
    {
        $this->transbank->setEnvironment('integration');
        $this->assertTrue($this->transbank->isIntegration());
        $this->assertFalse($this->transbank->isProduction());

        $this->transbank->setEnvironment('production');
        $this->assertFalse($this->transbank->isIntegration());
        $this->assertTrue($this->transbank->isProduction());
    }

    public function testSetAndGetDefault()
    {
        $this->transbank->setDefault('webpay', 'foo', 'bar');
        $this->assertEquals('bar', $this->transbank->getDefault('webpay', 'foo'));
        $this->assertNull($this->transbank->getDefault('anyService', 'foo'));
    }

    public function testExceptionOnSetDefaultInvalidService()
    {
        $this->expectException(InvalidServiceException::class);
        $this->transbank->setDefault('anyService', 'foo', 'bar');
    }

    public function testWebpay()
    {
        $mockLogger = new class extends NullLogger {
            public function foo() { return 'bar'; }
        };

        $this->transbank->setLogger($mockLogger);

        $webpay = $this->transbank->webpay();

        $this->assertInstanceOf(Webpay::class, $webpay);
        $this->assertInstanceOf(NullLogger::class, $webpay->getLogger());
        $this->assertEquals('bar', $webpay->getLogger()->foo());
    }

    public function testOnepay()
    {
        $mockLogger = new class extends NullLogger {
            public function foo() { return 'bar'; }
        };

        $this->transbank->setLogger($mockLogger);

        $onepay = $this->transbank->onepay();

        $this->assertInstanceOf(Onepay::class, $onepay);
        $this->assertInstanceOf(NullLogger::class, $onepay->getLogger());
        $this->assertEquals('bar', $onepay->getLogger()->foo());
    }
}
