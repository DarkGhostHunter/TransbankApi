<?php


namespace Tests\Unit\Services;


use PHPUnit\Framework\TestCase;
use DarkGhostHunter\TransbankApi\Exceptions\Credentials\CredentialInvalidException;
use DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse;
use DarkGhostHunter\TransbankApi\Transbank;
use DarkGhostHunter\TransbankApi\Webpay;

class WebpayTest extends TestCase
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
        $webpayIntegration = Webpay::fromConfig($this->transbankIntegration);
        $webpayProduction = Webpay::fromConfig($this->transbankProduction);

        $this->assertInstanceOf(Webpay::class, $webpayIntegration);
        $this->assertInstanceOf(Webpay::class, $webpayProduction);
    }

    public function testInstancesFromConstructTransbank()
    {
        $webpayIntegration = new Webpay($this->transbankIntegration);
        $webpayProduction = new Webpay($this->transbankProduction);

        $this->assertInstanceOf(Webpay::class, $webpayIntegration);
        $this->assertInstanceOf(Webpay::class, $webpayProduction);
    }

    public function testServiceReturnsEnvironment()
    {
        $webpayIntegration = Webpay::fromConfig($this->transbankIntegration);
        $webpayProduction = Webpay::fromConfig($this->transbankProduction);

        $this->assertTrue($webpayIntegration->isIntegration());
        $this->assertFalse($webpayIntegration->isProduction());

        $this->assertTrue($webpayProduction->isProduction());
        $this->assertFalse($webpayProduction->isIntegration());
    }

    public function testCommitsWithNoCredentialsReturnsException()
    {
        $this->markTestSkipped();
    }

    public function testNoIntegrationCredentialsReturnsException()
    {
        $this->expectException(CredentialInvalidException::class);
        $this->markTestSkipped();
    }
}