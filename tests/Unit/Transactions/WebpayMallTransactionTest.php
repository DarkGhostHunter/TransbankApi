<?php

namespace Tests\Unit\Transactions;

use PHPUnit\Framework\TestCase;
use Transbank\Wrapper\Webpay;
use Transbank\Wrapper\Exceptions\Webpay\ServiceSdkUnavailableException;
use Transbank\Wrapper\TransbankConfig;

class WebpayMallTransactionTest extends TestCase
{
    /** @var Webpay */
    protected $webpay;

    protected function setUp()
    {
        $transbank = TransbankConfig::environment();

        $transbank->setDefaults('webpay', [
            'plusReturnUrl'         => 'http://app.com/webpay/result',
            'plusFinalUrl'          => 'http://app.com/webpay/receipt',
            'plusMallReturnUrl'     => 'http://app.com/webpay/mall/result',
            'plusMallFinalUrl'      => 'http://app.com/webpay/mall/receipt',
            'oneclickResponseURL'   => 'http://app.com/webpay/registration',
        ]);

        $this->webpay = Webpay::fromConfig(
            $transbank
        );
    }


    public function testReceivesOrdersAsItems()
    {
        $this->markTestSkipped();
    }

    public function testServiceSdkUnavailableExceptionOnOneclickMallCharge()
    {
        $this->expectException(ServiceSdkUnavailableException::class);

        $charges = $this->webpay->createMallCharge([
            'buyOrder' => 'master-store-order#65987',
            'tbkUser' => 'tbkUser',
            'username' => 'username',
            'storesInput' => [
                [
                    'storeCode' => 597044444402,
                    'amount' => 4990,
                    'buyOrder' => '201',
                    'sessionId' => 'alpha-session-id-1',
                    'sharesNumber' => 3
                ],
            ],
        ]);
    }

    public function testServiceSdkUnavailableExceptionOnOneclickMallReverse()
    {
        $this->expectException(ServiceSdkUnavailableException::class);

        $reverse = $this->webpay->createMallReverseCharge([
            'buyOrder' => 'store-order#123',
        ]);
    }

    public function testServiceSdkUnavailableExceptionOnOneclickMallNullify()
    {
        $this->expectException(ServiceSdkUnavailableException::class);

        $reverse = $this->webpay->createMallNullify([
            'authorizationCode' => 'makoy123',
            'commerceId' => 'store-child-1',
            'buyOrder' => '20181201153000001',
            'authorizedAmount' => 19990,
            'nullifyAmount' => 10000
        ]);
    }

    public function testServiceSdkUnavailableExceptionOnOneclickMallReverseNullify()
    {
        $this->expectException(ServiceSdkUnavailableException::class);

        $reverse = $this->webpay->createMallReverseNullify([
            'commerceId' => 'store-child-1',
            'buyOrder' => '20181201153000001',
            'nullifyAmount' => 10000
        ]);
    }
}