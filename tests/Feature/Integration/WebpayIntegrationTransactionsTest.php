<?php

namespace Tests\Feature\Integration;

use PHPUnit\Framework\TestCase;
use DarkGhostHunter\TransbankApi\Transbank;
use DarkGhostHunter\TransbankApi\Webpay;
use DarkGhostHunter\TransbankApi\Responses\WebpayOneclickResponse;
use DarkGhostHunter\TransbankApi\Responses\WebpayPlusMallResponse;
use DarkGhostHunter\TransbankApi\Responses\WebpayPlusResponse;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\InvalidWebpayTransactionException;

class WebpayIntegrationTransactionsTest extends TestCase
{

    /** @var Webpay */
    protected $webpay;

    protected function setUp() : void
    {
        $transbank = Transbank::make('integration');

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
        $this->assertIsString($normal->token);
        $this->assertIsString(filter_var($normal->url, FILTER_VALIDATE_URL));

    }

    public function testSendsInvalidPlusNormal()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $this->webpay->createNormal([
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
                    'commerceCode' => 597044444402,
                    'amount' => 2000,
                    'buyOrder' => '597044444402-1',
                    'sessionId' => 'session-id-1'
                ],
                [
                    'commerceCode' => 597044444403,
                    'amount' => 2000,
                    'buyOrder' => '597044444403-1',
                    'sessionId' => 'session-id-2'
                ],

            ]
        ]);

        $this->assertInstanceOf(WebpayPlusMallResponse::class, $normal);
        $this->assertIsString($normal->token);
        $this->assertIsString(filter_var($normal->url, FILTER_VALIDATE_URL));
    }

    public function testCommitsInvalidPlusMallNormal()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $this->webpay->createMallNormal([
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

        $this->webpay->createCapture([
            'authorizationCode' => '!found',
            'captureAmount' => 2000,
            'buyOrder' => 'testBuyOrder',
            'captureAmount ' => 2000,
        ]);

        $this->webpay->createMallCapture([
            'sessionId' => 'client-session-id-88',
            'buyOrder' => 'myOrder#16548',
            'amount' => 1000,
            'commerceOrder' => 597044444402
        ]);
    }

    public function testCommitsInvalidNullify()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $this->webpay->createNullify([
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

        $this->assertInstanceOf(WebpayOneclickResponse::class, $registration);
        $this->assertIsString($registration->token);
        $this->assertIsString(filter_var($registration->url, FILTER_VALIDATE_URL));
    }

    public function testCommitsInvalidOneclickUnregistration()
    {
        $unregistration  = $this->webpay->createUnregistration([
            'username' => 'appusername',
            'tbkUser' => 'tbkUser',
        ]);

        $this->assertFalse($unregistration->isSuccess());
    }

    public function testCommitsInvalidCharge()
    {
        $this->expectException(InvalidWebpayTransactionException::class);

        $this->webpay->createCharge([
            'amount' => 9990,
            'buyOrder' => 7,
            'tbkUser' => 'tbkUser',
            'username' => 'appusername',
        ]);
    }


    public function testCommitsInvalidOneclickReverseCharge()
    {
        $reverse = $this->webpay->createReverseCharge([
            'buyOrder' => 20202020120001001
        ]);

        $this->assertFalse($reverse->isSuccess());
    }


}