<?php

namespace Tests\Unit\Services;

use BadMethodCallException;
use DarkGhostHunter\TransbankApi\AbstractService;
use DarkGhostHunter\TransbankApi\Contracts;
use DarkGhostHunter\TransbankApi\ResponseFactories\AbstractResponseFactory;
use DarkGhostHunter\TransbankApi\TransactionFactories\AbstractTransactionFactory;
use DarkGhostHunter\TransbankApi\Transactions\AbstractTransaction;
use DarkGhostHunter\TransbankApi\Transbank;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class AbstractServiceTest extends TestCase
{

    /** @var AbstractService */
    protected $service;

    /** @var LoggerInterface&\Mockery\MockInterface */
    protected $mockLogger;

    /** @var Transbank&\Mockery\MockInterface */
    protected $mockTransbank;

    protected function setUp()
    {
        $this->mockTransbank = \Mockery::mock(Transbank::class);

        $this->mockLogger = \Mockery::mock(LoggerInterface::class);

        $this->mockTransbank->shouldReceive('getDefaults')
            ->once()
            ->andReturn([
                'foo' => 'bar'
            ]);

        $this->mockTransbank->shouldReceive('getCredentials')
            ->once()
            ->andReturn([
                'baz' => 'qux'
            ]);

        $this->service = new class ($this->mockTransbank, $this->mockLogger) extends AbstractService {
            public function bootAdapter() {}
            public function bootTransactionFactory() {}
            protected function getProductionCredentials() {
                return ['foo' => 'bar'];
            }

            protected function getIntegrationCredentials(string $type = null) {
                return ['foo' => 'bar'];
            }
            protected function parseResponse(array $result, $options = null) {
                return [$result, $options];
            }
        };
    }

    public function testIsIntegration()
    {
        $this->mockTransbank->shouldReceive('isIntegration')
            ->once()
            ->andReturnTrue();

        $this->service->isIntegration();

        $this->assertTrue(true);
    }

    public function testIsProduction()
    {
        $this->mockTransbank->shouldReceive('isProduction')
            ->once()
            ->andReturnTrue();

        $this->service->isProduction();

        $this->assertTrue(true);
    }

    public function testSetAndGetAdapter()
    {
        $this->service->setAdapter(\Mockery::mock(Contracts\AdapterInterface::class));

        $this->assertInstanceOf(Contracts\AdapterInterface::class, $this->service->getAdapter());
    }

    public function testSetAndGetTransactionFactory()
    {
        $this->service->setTransactionFactory(\Mockery::mock(AbstractTransactionFactory::class));

        $this->assertInstanceOf(AbstractTransactionFactory::class, $this->service->getTransactionFactory());
    }

    public function testCommit()
    {
        $mockTransaction = \Mockery::mock(Contracts\TransactionInterface::class);

        $mockAdapter = \Mockery::mock(Contracts\AdapterInterface::class);

        $mockTransaction->shouldReceive('getType')
            ->andReturn('mock.test');

        $mockAdapter->shouldReceive('setCredentials')
            ->andReturn(['foo' => 'bar']);

        $mockAdapter->shouldReceive('commit')
            ->andReturn(['baz' => 'qux']);

        $this->mockLogger->shouldReceive('info')
            ->with(\Mockery::type('string'))
            ->andReturnNull();

        $this->mockTransbank->shouldReceive('isProduction')->once()
            ->andReturnFalse();

        $this->service->setAdapter($mockAdapter);

        $response = $this->service->commit($mockTransaction);

        $this->assertEquals([['baz' => 'qux'], 'mock.test'], $response);

        $this->mockTransbank->shouldReceive('isProduction')->once()
            ->andReturnTrue();

        $this->service->setAdapter($mockAdapter);

        $response = $this->service->commit($mockTransaction);

        $this->assertEquals([['baz' => 'qux'], 'mock.test'], $response);

    }

    public function testSetAndGetLogger()
    {
        $mockLogger = new class extends NullLogger {
            public function foo() { return 'bar'; }
        };

        $this->service->setLogger($mockLogger);
        $this->assertEquals('bar', $this->service->getLogger()->foo());
    }

    public function testGetTransaction()
    {
        $mockAdapter = \Mockery::mock(Contracts\AdapterInterface::class);

        $mockAdapter->shouldReceive('setCredentials')
            ->andReturn(['foo' => 'bar']);

        $mockAdapter->shouldReceive('retrieveAndConfirm')->once()
            ->with(\Mockery::type('string'), \Mockery::type('string'))
            ->andReturn(['baz' => 'qux']);

        $this->mockLogger->shouldReceive('info')
            ->with(\Mockery::type('string'))
            ->andReturnNull();

        $this->mockTransbank->shouldReceive('isProduction')->once()
            ->andReturnFalse();

        $this->service->setAdapter($mockAdapter);

        $response = $this->service->getTransaction('test.transaction', 'test.type');

        $this->assertEquals([['baz' => 'qux'], 'test.type'], $response);
    }

    public function testForwardCallToTransactionFactory()
    {
        $mockFactory = new class($this->service) extends AbstractTransactionFactory {
            public function createMockTransaction(array $array) { return $array; }
            protected function makeTransaction(string $type, array $attributes = []) { }
        };

        $this->service->setTransactionFactory($mockFactory);

        $transaction = $this->service->createMockTransaction($array = ['foo' => 'bar']);

        $this->assertEquals($array, $transaction);
    }

    public function testExceptionOnForwardInvalidCallToTransactionFactory()
    {
        $this->expectException(BadMethodCallException::class);

        $mockFactory = new class($this->service) extends AbstractTransactionFactory {
            public function createMockTransaction(array $array) { return $array; }
            protected function makeTransaction(string $type, array $attributes = []) { }
        };

        $this->service->setTransactionFactory($mockFactory);

        $this->service->invalidMockTransaction($array = ['foo' => 'bar']);
    }

    public function testSetAndGetResponseFactory()
    {
        $mockFactory = \Mockery::mock(AbstractResponseFactory::class);

        $this->service->setResponseFactory($mockFactory);

        $this->assertInstanceOf(AbstractResponseFactory::class, $this->service->getResponseFactory());
    }

    public function testForwardsCallToResponseFactory()
    {
        $mockFactory = new class($this->service) extends AbstractResponseFactory {
            public function confirmTestResponse(array $array) { return $array; }
        };

        $this->service->setResponseFactory($mockFactory);

        $transaction = $this->service->confirmTestResponse($array = ['foo' => 'bar']);

        $this->assertEquals($array, $transaction);
    }

    public function testExceptionOnForwardsInvalidCallToResponseFactory()
    {
        $this->expectException(BadMethodCallException::class);

        $mockFactory = new class($this->service) extends AbstractResponseFactory {
            public function confirmTestResponse(array $array) { return $array; }
        };

        $this->service->setResponseFactory($mockFactory);

        $this->service->invalidTestResponse($array = ['foo' => 'bar']);
    }

    public function testFromConfig()
    {
        $this->mockTransbank->shouldReceive('getLogger')
            ->andReturn(new NullLogger());

        $service = $this->service::fromConfig($this->mockTransbank);

        $this->assertInstanceOf(Contracts\ServiceInterface::class, $service);
    }

    public function testCredentialsDirectory()
    {
        $dir = $this->service->credentialsDirectory();

        $this->assertIsString($dir);
        $this->assertContains('/../credentials', $dir);
    }

    public function testEnvironmentCredentialsDirectory()
    {
        $this->mockTransbank->shouldReceive('getEnvironment')
            ->once()
            ->andReturn('mock-dir');

        $dir = $this->service->environmentCredentialsDirectory();

        $this->assertContains('mock-dir', $dir);
    }
}
