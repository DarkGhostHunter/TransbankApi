<?php

namespace Tests\Browser;

use DarkGhostHunter\TransbankApi\Transbank;
use DarkGhostHunter\TransbankApi\Webpay;
use Symfony\Component\Panther\PantherTestCase;

class CreatesWebpayNormal extends PantherTestCase
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

    public function testMyApp()
    {
        $normal = $this->webpay->createNormal([
            'returnUrl' => 'http://app.com/webpay/return',
            'finalUrl' => 'http://app.com/webpay/final',
            'amount' => 2000,
            'buyOrder' => 'testBuyOrder',
        ]);

        $client = static::createPantherClient();
        $crawler = $client->request('POST', '/mypage');
        $this->assertContains('Webpay', $crawler->filter('title')->html());
    }
}
