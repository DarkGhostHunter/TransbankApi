<?php

namespace Tests\Unit\Transbank;

use PHPUnit\Framework\TestCase;
use DarkGhostHunter\TransbankApi\Exceptions\Credentials\CredentialInvalidException;
use DarkGhostHunter\TransbankApi\Exceptions\Transbank\InvalidServiceException;
use DarkGhostHunter\TransbankApi\Transbank;

class TransbankTest extends TestCase
{

    protected $mockCredentials = [
        'commerceCode' => '5000000001',
        'publicKey' => 'ABCD1234EF...',
        'publicCert' => '---BEGIN CERTIFICATE---...'
    ];

    protected $mockDefaults = [
        'plusReturnUrl' => 'http://app.com/webpay/normal/result',
        'plusFinalUrl' => 'http://app.com/webpay/normal/receipt',
        'plusMallReturnUrl' => 'http://app.com/webpay/mall/result',
        'plusMallFinalUrl' => 'http://app.com/webpay/mall/receipt',
        'oneclickReturnUrl' => 'http://app.com/webpay/oneclick/result',
    ];

    /**
     * Transbank Config should return an integration instance if the
     * environment is not explicitly 'production'
     *
     * @throws \Exception
     */
    public function testCreatesIntegrationEnvironment()
    {
        $transbank_a = Transbank::environment();
        $transbank_b = Transbank::environment('notProduction');
        $transbank_c = Transbank::environment('integration');

        $this->assertInstanceOf(Transbank::class, $transbank_a);
        $this->assertInstanceOf(Transbank::class, $transbank_b);
        $this->assertInstanceOf(Transbank::class, $transbank_c);

        $this->assertTrue($transbank_a->isIntegration());
        $this->assertTrue($transbank_b->isIntegration());
        $this->assertTrue($transbank_c->isIntegration());

        $this->assertFalse($transbank_a->isProduction());
        $this->assertFalse($transbank_b->isProduction());
        $this->assertFalse($transbank_c->isProduction());

        $this->assertEquals('integration', $transbank_a->getEnvironment());
        $this->assertEquals('integration', $transbank_b->getEnvironment());
        $this->assertEquals('integration', $transbank_c->getEnvironment());
    }

    public function testCreatesProductionEnvironment()
    {
        $transbank = Transbank::environment('production');

        $this->assertInstanceOf(Transbank::class, $transbank);
        $this->assertTrue($transbank->isProduction());
        $this->assertFalse($transbank->isIntegration());

        $this->assertEquals('production', $transbank->getEnvironment());
    }

    public function testSetProductionCredentialsAtInstancing()
    {
        $transbank = Transbank::environment('production', [
            'webpay' => $this->mockCredentials
        ]);

        $this->assertEquals($this->mockCredentials, $transbank->getCredentials('webpay')->toArray(),
            '', 0.0, 10, true);
    }

    public function testExceptionOnCredentialSetForInvalidServiceOnInstance()
    {
        $this->expectException(\Exception::class);

        Transbank::environment('production', [
            'invalid_service' => $this->mockCredentials
        ]);
    }

    public function testSetsProductionCredentialsOnMethod()
    {
        $transbank = Transbank::environment('production');

        $transbank->setCredentials('webpay', $this->mockCredentials);

        $this->assertEquals($this->mockCredentials, $transbank->getCredentials('webpay')->toArray(),
            '', 0.0, 10, true);
    }

    public function testExceptionOnCredentialSetForInvalidServiceOnMethod()
    {
        $this->expectException(\Exception::class);

        $transbank = Transbank::environment('production');

        $transbank->setCredentials('INVALID_SERVICE', $this->mockCredentials);
    }

    public function testSetsDefaults()
    {
        $transbank_integration = Transbank::environment();
        $transbank_production = Transbank::environment('production');

        $transbank_integration->setDefaults('webpay', $this->mockDefaults);
        $transbank_production->setDefaults('webpay', $this->mockDefaults);

        $this->assertEquals($this->mockDefaults, $transbank_integration->getDefaults('webpay'),
            '', 0.0, 10, true);
        $this->assertEquals($this->mockDefaults, $transbank_production->getDefaults('webpay'),
            '', 0.0, 10, true);
    }

    public function testExceptionOnSetsDefaultsOnIntegrationOnInvalidService()
    {
        $this->expectException(\Exception::class);

        $transbank = Transbank::environment();

        $transbank->setDefaults('INVALID_SERVICE', $this->mockDefaults);
    }

    public function testExceptionOnSetsDefaultsOnProductionOnInvalidService()
    {
        $this->expectException(\Exception::class);

        $transbank = Transbank::environment('production');

        $transbank->setDefaults('INVALID_SERVICE', $this->mockDefaults);
    }

    public function testRetrievesDefaultOption()
    {
        $transbank = Transbank::environment('production');

        $transbank->setDefaults('webpay', $this->mockDefaults);

        $defaultKey = key($this->mockDefaults);

        $defaultValue = $this->mockDefaults[$defaultKey];

        $this->assertEquals($defaultValue, $transbank->getDefault('webpay', $defaultKey));
    }

    public function testReturnsNullOnInvalidDefaultOption()
    {
        $transbank = Transbank::environment('production');

        $transbank->setDefaults('webpay', $this->mockDefaults);

        $this->assertNull($transbank->getDefault('webpay', 'INVALID_DEFAULT'));
    }

    public function testReturnsNullOnInvalidServiceOption()
    {
        $transbank = Transbank::environment('production');

        $transbank->setDefaults('webpay', $this->mockDefaults);

        $defaultKey = key($this->mockDefaults);

        $this->assertNull($transbank->getDefault('INVALID_SERVICE', $defaultKey));
    }

    public function testCredentialInvalidException()
    {
        $this->expectException(CredentialInvalidException::class);

        $transbank = Transbank::environment('production');

        $transbank->setCredentials('webpay', [
            'asdads' => [],
            'asdasd' => new \stdClass(),
            'asdads' => 654654654.5454
        ]);
    }

    public function testSettingCredentialsForInvalidServiceReturnsException()
    {
        $this->expectException(InvalidServiceException::class);

        $transbank = Transbank::environment('production');

        $transbank->setCredentials('asdasdasd', [
            'asdads' => [],
            'asdasd' => new \stdClass(),
            'asdads' => 654654654.5454
        ]);
    }


}