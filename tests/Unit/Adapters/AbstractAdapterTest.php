<?php

namespace Tests\Unit\Adapters;

use DarkGhostHunter\Fluid\Fluid;
use DarkGhostHunter\TransbankApi\Adapters\AbstractAdapter;
use DarkGhostHunter\TransbankApi\Contracts\TransactionInterface;
use PHPUnit\Framework\TestCase;

class AbstractAdapterTest extends TestCase
{

    /** @var AbstractAdapter */
    protected $adapter;

    protected function setUp() : void
    {
        $this->adapter = new class extends AbstractAdapter {
            public function getCredentials() { return $this->credentials; }
            public function commit(TransactionInterface $transaction, $options = null) {}
            public function retrieveAndConfirm($transaction, $options = null) {}

            public function __construct()
            {
            }
        };
    }

    public function testSetAndGetClient()
    {
        $this->adapter->setClient(new Fluid(['foo' => 'bar']));

        $this->assertEquals('bar', $this->adapter->getClient()->foo);
    }

    public function testSetCredentials()
    {
        $this->adapter->setCredentials(new Fluid(['foo' => 'bar']));

        $this->assertEquals('bar', $this->adapter->getCredentials()->foo);
    }

    public function testIsProduction()
    {
        $this->assertFalse($this->adapter->isProduction());
    }

    public function testSetIsProduction()
    {
        $this->adapter->setIsProduction(false);
        $this->assertFalse($this->adapter->isProduction());
        $this->adapter->setIsProduction(true);
        $this->assertTrue($this->adapter->isProduction());
    }
}
