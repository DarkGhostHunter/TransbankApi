<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use Transbank\Wrapper\Onepay;
use Transbank\Wrapper\Transactions\Cart;
use Transbank\Wrapper\Transactions\OnepayTransaction;
use Transbank\Wrapper\TransbankConfig;

class OnepayTest extends TestCase
{

    protected $transbankIntegration;

    protected $transbankProduction;

    protected function setUp()
    {
        $this->transbankIntegration = TransbankConfig::environment();
        $this->transbankProduction = TransbankConfig::environment('production');
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