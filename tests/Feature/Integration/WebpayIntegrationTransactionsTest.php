<?php

namespace Tests\Feature\Integration;

use PHPUnit\Framework\TestCase;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\InvalidWebpayTransactionException;
use DarkGhostHunter\TransbankApi\Responses\WebpayMallResult;
use DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse;
use DarkGhostHunter\TransbankApi\Transbank;
use DarkGhostHunter\TransbankApi\Webpay;

class WebpayIntegrationTransactionsTest extends TestCase
{

    /** @var Webpay */
    protected $webpay;

    protected function setUp()
    {
        $transbank = Transbank::environment();

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
    public function testCommitsPlusNormal()
    {
        $normal = $this->webpay->createNormal([
            'returnUrl' => 'http://app.com/webpay/return',
            'finalUrl' => 'http://app.com/webpay/final',
            'amount' => 2000,
            'buyOrder' => 'testBuyOrder',
        ]);

        $this->assertInstanceOf(WebpayPlusResponse::class, $normal);
        $this->assertTrue(is_string($normal->token));
        $this->assertTrue(is_string(filter_var($normal->url, FILTER_VALIDATE_URL)));

    }

    public function testSendsInvalidPlusNormal()
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

    public function testCommitsInvalidPlusMallNormal()
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

    public function testCommitsNotFoundPlusCapture()
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

    public function testCommitsInvalidNullify()
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

        $this->assertInstanceOf(WebpayPlusResponse::class, $registration);
        $this->assertTrue(is_string($registration->token));
        $this->assertTrue(is_string(filter_var($registration->urlWebpay, FILTER_VALIDATE_URL)));
    }

    public function testCommitsInvalidOneclickUnregistration()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $unregistration  = $this->webpay->createUnregistration([
            'username' => 'appusername',
            'tbkUser' => 'tbkUser',
        ]);
    }

    public function testCommitsInvalidCharge()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $charge = $this->webpay->createCharge([
            'amount' => 9990,
            'buyOrder' => 7,
            'tbkUser' => 'tbkUser',
            'username' => 'appusername',
        ]);
    }


    public function testCommitsInvalidOneclickReverseCharge()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $reverse = $this->webpay->createReverseCharge([
            'buyOrder' => 20202020120001001
        ]);
    }


}