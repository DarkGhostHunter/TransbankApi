<?php

namespace Tests\Unit\Adapters;

use DarkGhostHunter\TransbankApi\Adapters\OnepayAdapter;
use DarkGhostHunter\TransbankApi\Clients\AbstractClient;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\Transactions\OnepayNullifyTransaction;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;
use PHPUnit\Framework\TestCase;

class OnepayAdapterTest extends TestCase
{

    /** @var OnepayAdapter */
    protected $adapter;

    /** @var AbstractClient&\Mockery\MockInterface */
    protected $client;

    protected function setUp()
    {
        $this->adapter = new OnepayAdapter();

        $this->client = \Mockery::mock(AbstractClient::class, [
            true, new Fluent(['foo' => 'bar'])
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
