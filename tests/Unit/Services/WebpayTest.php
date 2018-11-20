<?php


namespace Tests\Unit\Services;


use PHPUnit\Framework\TestCase;
use Transbank\Wrapper\Exceptions\Credentials\CredentialInvalidException;
use Transbank\Wrapper\Results\WebpayResult;
use Transbank\Wrapper\TransbankConfig;
use Transbank\Wrapper\Webpay;

class WebpayTest extends TestCase
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