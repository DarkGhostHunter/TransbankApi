<?php

namespace Tests\Unit\Transactions;

use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use PHPUnit\Framework\TestCase;

class WebpayTransactionTest extends TestCase
{

    /** @var WebpayTransaction */
    protected $transaction;

    protected $defaults = [
        'plusReturnUrl'         => 'http://app.com/webpay/return',
        'plusFinalUrl'          => 'http://app.com/webpay/final',
        'plusMallReturnUrl'     => 'http://app.com/webpay/mall/return',
        'plusMallFinalUrl'      => 'http://app.com/webpay/mall/final',
        'oneclickResponseURL'   => 'http://app.com/webpay/response',
    ];

    protected function setUp() : void
{
        $this->transaction = new WebpayTransaction();
    }

    public function testSetsDefaultsForPlusNormal()
    {
        $this->transaction->setType('plus.normal');

        $this->transaction->setDefaults($this->defaults);

        $this->assertEquals('http://app.com/webpay/return', $this->transaction->returnUrl);
        $this->assertEquals('http://app.com/webpay/final', $this->transaction->finalUrl);
        $this->assertCount(2, $this->transaction->getAttributes());
    }

    public function testSetsDefaultsForPlusDefer()
    {
        $this->transaction->setType('plus.defer');

        $this->transaction->setDefaults($this->defaults);

        $this->assertEquals('http://app.com/webpay/return', $this->transaction->returnUrl);
        $this->assertEquals('http://app.com/webpay/final', $this->transaction->finalUrl);
        $this->assertCount(2, $this->transaction->getAttributes());
    }

    public function testSetsDefaultsForPlusMallNormal()
    {
        $this->transaction->setType('plus.mall.normal');

        $this->transaction->setDefaults($this->defaults);

        $this->assertEquals('http://app.com/webpay/mall/return', $this->transaction->returnUrl);
        $this->assertEquals('http://app.com/webpay/mall/final', $this->transaction->finalUrl);
        $this->assertCount(2, $this->transaction->getAttributes());
    }

    public function testSetsDefaultsForPlusMallDefer()
    {
        $this->transaction->setType('plus.mall.defer');

        $this->transaction->setDefaults($this->defaults);

        $this->assertEquals('http://app.com/webpay/mall/return', $this->transaction->returnUrl);
        $this->assertEquals('http://app.com/webpay/mall/final', $this->transaction->finalUrl);
        $this->assertCount(2, $this->transaction->getAttributes());
    }

    public function testSetsDefaultsForOneclickRegister()
    {
        $this->transaction->setType('oneclick.register');

        $this->transaction->setDefaults($this->defaults);

        $this->assertEquals('http://app.com/webpay/response', $this->transaction->responseURL);
        $this->assertCount(1, $this->transaction->getAttributes());
    }


}
