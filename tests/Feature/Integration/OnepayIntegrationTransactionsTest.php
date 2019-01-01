<?php

namespace Tests\Feature\Integration;

use DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepayClientException;
use PHPUnit\Framework\TestCase;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\CartEmptyException;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\CartNegativeAmountException;
use DarkGhostHunter\TransbankApi\Onepay;
use DarkGhostHunter\TransbankApi\Transbank;

class OnepayIntegrationTransactionsTest extends TestCase
{
    /** @var Onepay */
    protected $onepay;

    protected function setUp() : void
    {
        $transbank = Transbank::environment();

        $transbank->setDefaults('onepay', [
            'channel'               => 'mobile',
            'generateOttQrCode'     => true,
            'callbackUrl'           => 'http://app.com/onepay/result',
            'appScheme'             => 'my-app://onepay/result',
        ]);

        $this->onepay = Onepay::fromConfig(
            $transbank
        );
    }

    public function testCreatesCartInTransbank()
    {
        $cart = $this->onepay->createCart([
            'channel' => 'web',
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
    }

    public function testExceptionOnChannelAppWithoutScheme()
    {
        $this->expectException(OnepayClientException::class);

        $this->onepay->createCart([
            'channel' => 'app',
            'appScheme' => null,
            'items' => [
                'description' => 'Zapatos',
                'quantity' =>  1,
                'amount' => 15000
            ]
        ]);
    }

    public function testCreatedCartHidesSignatureOnSerialization()
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