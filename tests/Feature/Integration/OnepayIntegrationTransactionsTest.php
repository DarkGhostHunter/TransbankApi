<?php

namespace Tests\Feature\Integration;

use PHPUnit\Framework\TestCase;
use Transbank\Wrapper\Exceptions\Onepay\CartEmptyException;
use Transbank\Wrapper\Exceptions\Onepay\CartNegativeAmountException;
use Transbank\Wrapper\Exceptions\Onepay\OnepaySdkException;
use Transbank\Wrapper\Onepay;
use Transbank\Wrapper\TransbankConfig;

class OnepayIntegrationTransactionsTest extends TestCase
{
    /** @var Onepay */
    protected $onepay;

    protected function setUp() : void
    {
        $transbank = TransbankConfig::environment();

        $transbank->setDefaults('onepay', [
            'channel'               => 'mobile',
            'generateOttQrCode'     => false,
            'callbackUrl'           => 'http://app.com/onepay/result',
            'appScheme'             => 'my-app://onepay/result',
        ]);

        $this->onepay = Onepay::fromConfig(
            $transbank
        );

        parent::setUp();
    }

    public function testCreatesCartInTransbank()
    {
        $cart = $this->onepay->createCart([
            'channel' => 'APP',
            'items' => [
                'description' => 'Zapatos',
                'quantity' =>  1,
                'amount' => 15000
            ]
        ]);

        $this->assertTrue($cart->isSuccess());
        $this->assertNotEmpty($cart->ott);
        $this->assertNotEmpty($cart->externalUniqueNumber);
        $this->assertNotEmpty($cart->qrCodeAsBase64);
        $this->assertNotEmpty($cart->issuedAt);
        $this->assertNotEmpty($cart->signature);
        $this->assertEquals('OK', $cart->responseCode);
        $this->assertEquals('OK', $cart->description);
        $this->assertNotEmpty($cart->amount);
    }

    public function testExceptionOnChannelAppWithoutScheme()
    {

        $this->expectException(OnepaySdkException::class);

        $cart = $this->onepay->createCart([
            'channel' => 'app',
            'appScheme' => null,
            'items' => [
                'description' => 'Zapatos',
                'quantity' =>  1,
                'amount' => 15000
            ]
        ]);
    }

    public function testExcCreatedCartHidesSignatureOnSerialization()
    {
        $cart = $this->onepay->createCart([
            'items' => [
                'description' => 'Zapatos',
                'quantity' =>  1,
                'amount' => 15000
            ]
        ]);

        $this->assertFalse(strpos($cart->toJson(), 'signature'));
        $this->assertFalse(strpos((string)$cart, 'signature'));
    }

    public function testExceptionOnCartWithZeroAmount()
    {
        $this->expectException(CartNegativeAmountException::class);

        $cart = $this->onepay->createCart([
            'items' => [
                'description' => 'Zapatos',
                'quantity' =>  10,
                'amount' => -15000
            ]
        ]);
    }

    public function testExceptionOnEmptyCart()
    {
        $this->expectException(CartEmptyException::class);

        $cart = $this->onepay->createCart([
            'items' => []
        ]);
    }
}