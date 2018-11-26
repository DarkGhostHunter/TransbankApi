<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use DarkGhostHunter\TransbankApi\Onepay;
use DarkGhostHunter\TransbankApi\Transactions\Cart;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;
use DarkGhostHunter\TransbankApi\Transbank;

class OnepayTest extends TestCase
{

    protected $transbankIntegration;

    protected $transbankProduction;

    protected function setUp()
    {
        $this->transbankIntegration = Transbank::environment();
        $this->transbankProduction = Transbank::environment('production');
    }

    public function testInstancesFromReceivingTransbank()
    {
        $onepayIntegration = Onepay::fromConfig($this->transbankIntegration);
        $webpayProduction = Onepay::fromConfig($this->transbankProduction);

        $this->assertInstanceOf(Onepay::class, $onepayIntegration);
        $this->assertInstanceOf(Onepay::class, $webpayProduction);
    }

    public function testInstancesFromConstructTransbank()
    {
        $onepayIntegration = new Onepay($this->transbankIntegration);
        $webpayProduction = new Onepay($this->transbankProduction);

        $this->assertInstanceOf(Onepay::class, $onepayIntegration);
        $this->assertInstanceOf(Onepay::class, $webpayProduction);
    }

    public function testServiceReturnsEnvironment()
    {
        $onepayIntegration = Onepay::fromConfig($this->transbankIntegration);
        $webpayProduction = Onepay::fromConfig($this->transbankProduction);

        $this->assertTrue($onepayIntegration->isIntegration());
        $this->assertFalse($onepayIntegration->isProduction());

        $this->assertTrue($webpayProduction->isProduction());
        $this->assertFalse($webpayProduction->isIntegration());
    }
    
    public function testCartAliased()
    {
        $onepayIntegration = Onepay::fromConfig($this->transbankIntegration);
        $webpayProduction = Onepay::fromConfig($this->transbankProduction);

        $this->assertTrue($onepayIntegration->makeCart() instanceof OnepayTransaction);
        $this->assertTrue($webpayProduction->makeCart() instanceof OnepayTransaction);
    }
}