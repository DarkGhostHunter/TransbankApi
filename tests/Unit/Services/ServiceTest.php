<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{

    public function testReceivesTransbankConfigIntegration()
    {
        $this->markTestSkipped();
    }

    public function testReceivesTransbankConfigProduction()
    {
        $this->markTestSkipped();
    }

    public function testReceivesDefaults()
    {
        $this->markTestSkipped();
    }

    public function testReceivesCredentials()
    {
        $this->markTestSkipped();
    }

    public function testReceivesAdapter()
    {
        $this->markTestSkipped();
    }

    public function testReceivesFactory()
    {
        $this->markTestSkipped();
    }

    public function testCanBoot()
    {
        $this->markTestSkipped();
    }

    public function testReturnsEnvironmentBooleans()
    {
        $this->markTestSkipped();
    }

    public function testPerformsTransaction()
    {
        $this->markTestSkipped();
    }

    public function testConfirmsTransaction()
    {
        $this->markTestSkipped();
    }

    public function testGetTransaction()
    {
        $this->markTestSkipped();
    }

    public function testAcknowledgeTransaction()
    {
        $this->markTestSkipped();
    }

    public function testCommittingReturnsTransbankResultInstance()
    {
        $this->markTestSkipped();
    }

    public function testCredentialsNotSetException()
    {
        // The services tries to commit a transaction, but the adapter finds no credentials
        $this->markTestSkipped();
    }


}