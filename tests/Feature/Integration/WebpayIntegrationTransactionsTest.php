<?php

namespace Tests\Feature\Integration;

use PHPUnit\Framework\TestCase;
use Transbank\Wrapper\Exceptions\Webpay\InvalidWebpayTransactionException;
use Transbank\Wrapper\Exceptions\Webpay\ServiceSdkUnavailableException;
use Transbank\Wrapper\Results\WebpayMallResult;
use Transbank\Wrapper\Results\WebpayResult;
use Transbank\Wrapper\TransbankConfig;
use Transbank\Wrapper\Webpay;

class WebpayIntegrationTransactionsTest extends TestCase
{

    /** @var Webpay */
    protected $webpay;

    protected function setUp() : void
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

    /**
     * @group selenium
     */
    public function testStartsPlusNormal()
    {
        $normal = $this->webpay->createNormal([
            'returnUrl' => 'http://app.com/webpay/return',
            'finalUrl' => 'http://app.com/webpay/final',
            'amount' => 2000,
            'buyOrder' => 'testBuyOrder',
        ]);

        $this->assertInstanceOf(WebpayResult::class, $normal);
        $this->assertTrue(is_string($normal->token));
        $this->assertTrue(is_string(filter_var($normal->url, FILTER_VALIDATE_URL)));

    }

    public function testStartsInvalidPlusNormal()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $normal = $this->webpay->createNormal([
            'returnUrl' => 'invalidReturnUrl',
            'finalUrl' => 'invalidFinalUrl',
            'amount' => -2000,
            'buyOrder' => -9999,
        ]);
    }

    public function testCreatesPlusMallNormal()
    {
        $normal = $this->webpay->createMallNormal([
            'buyOrder' => 'parent-order-123123',
            'items' => [
                [
                    'storeCode' => 597044444402,
                    'amount' => 2000,
                    'buyOrder' => '597044444402-1',
                    'sessionId' => 'session-id-1'
                ],
                [
                    'storeCode' => 597044444403,
                    'amount' => 2000,
                    'buyOrder' => '597044444403-1',
                    'sessionId' => 'session-id-2'
                ],

            ]
        ]);

        $this->assertInstanceOf(WebpayMallResult::class, $normal);
        $this->assertTrue(is_string($normal->token));
        $this->assertTrue(is_string(filter_var($normal->url, FILTER_VALIDATE_URL)));
    }

    public function testCreatesInvalidPlusMallNormal()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $normal = $this->webpay->createMallNormal([
            'buyOrder' => 'parent-order-123123',
            'items' => [
                [
                    'storeCode' => 597044444402,
                    'amount' => -2000,
                    'buyOrder' => -9999,
                    'sessionId' => -9999
                ],
                [
                    'storeCode' => 597044444403,
                    'amount' => -2000,
                    'buyOrder' => -9999,
                    'sessionId' => -9999
                ],
            ]
        ]);
    }

    public function testCreatesNotFoundPlusCapture()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $normal = $this->webpay->createCapture([
            'authorizationCode' => '!found',
            'captureAmount' => 2000,
            'buyOrder' => 'testBuyOrder',
            'captureAmount ' => 2000,
        ]);

        $mall = $this->webpay->createMallCapture([
            'sessionId' => 'client-session-id-88',
            'buyOrder' => 'myOrder#16548',
            'amount' => 1000,
            'commerceOrder' => 597044444402
        ]);
    }

    public function testCreatesInvalidNullify()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $nullify = $this->webpay->createNullify([
            'authorizationCode' => '03fr4E',
            'authorizedAmount' => 19990,
            'buyOrder' => 'store-order-32154',
            'nullifyAmount' => 10000,
        ]);
    }

    public function testCreatesOneclickRegistration()
    {
        $registration  = $this->webpay->createRegistration([
            'username' => 'appusername',
            'email' => 'username@gmail.com',
            'responseUrl' => 'https://app.com/oneclick/result'
        ]);

        $this->assertInstanceOf(WebpayResult::class, $registration);
        $this->assertTrue(is_string($registration->token));
        $this->assertTrue(is_string(filter_var($registration->urlWebpay, FILTER_VALIDATE_URL)));
    }

    public function testCreatesInvalidOneclickUnregistration()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $unregistration  = $this->webpay->createUnregistration([
            'username' => 'appusername',
            'tbkUser' => 'tbkUser',
        ]);
    }

    public function testCreatesInvalidCharge()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $charge = $this->webpay->createCharge([
            'amount' => 9990,
            'buyOrder' => 7,
            'tbkUser' => 'tbkUser',
            'username' => 'appusername',
        ]);
    }


    public function testCreatesInvalidOneclickReverseCharge()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $reverse = $this->webpay->createReverseCharge([
            'buyOrder' => 20202020120001001
        ]);
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