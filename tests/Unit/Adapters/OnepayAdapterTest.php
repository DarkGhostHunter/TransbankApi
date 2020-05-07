<?php

namespace Tests\Unit\Adapters;

use DarkGhostHunter\Fluid\Fluid;
use DarkGhostHunter\TransbankApi\Adapters\OnepayAdapter;
use DarkGhostHunter\TransbankApi\Clients\AbstractClient;
use DarkGhostHunter\TransbankApi\Clients\Onepay\OnepayClient;
use DarkGhostHunter\TransbankApi\Transactions\OnepayNullifyTransaction;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class OnepayAdapterTest extends TestCase
{

    /** @var OnepayAdapter */
    protected $adapter;

    /** @var AbstractClient&\Mockery\MockInterface */
    protected $client;

    protected function setUp() : void
    {
        $this->adapter = new OnepayAdapter();

        $this->client = \Mockery::mock(AbstractClient::class, [
            true, new Fluid(['foo' => 'bar'])
        ]);

        $this->adapter->setClient($this->client);
    }

    public function testCommit()
    {
        $this->client->allows([
            'commit' => true,
            'refund' => true,
        ]);

        $transaction = new OnepayTransaction([ 'foo' => 'bar' ]);
        $transaction->setType('onepay.cart');
        $response = $this->adapter->commit($transaction);
        $this->assertTrue($response);

        $transaction = new OnepayNullifyTransaction([ 'foo' => 'bar' ]);
        $transaction->setType('onepay.nullify');
        $response = $this->adapter->commit($transaction);
        $this->assertTrue($response);
    }

    public function testDoesntInstancesClientAgain()
    {
        $client = \Mockery::mock('overload:' . OnepayClient::class);

        $client->expects('boot');
        $client->shouldReceive('commit')->andReturnTrue();

        $this->adapter->setClient(null);

        $transaction = new OnepayTransaction([ 'foo' => 'bar' ]);
        $transaction->setType('onepay.cart');
        $response = $this->adapter->commit($transaction);
        $this->assertTrue($response);
    }

    public function testNullOnCommitUnavailable()
    {
        $transaction = new OnepayTransaction([ 'foo' => 'bar' ]);
        $transaction->setType('anthing');
        $response = $this->adapter->commit($transaction);
        $this->assertNull($response);
    }

    public function testRetrieveAndConfirm()
    {
        $this->client->allows([
            'confirm' => true,
        ]);

        $response = $this->adapter->retrieveAndConfirm(['transaction']);
        $this->assertTrue($response);
    }
}
