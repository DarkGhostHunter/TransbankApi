<?php

namespace Tests\Unit\Adapters;

use DarkGhostHunter\TransbankApi\Adapters\WebpayAdapter;
use DarkGhostHunter\TransbankApi\Clients\AbstractClient;
use DarkGhostHunter\TransbankApi\Exceptions\Webpay\ServiceSdkUnavailableException;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\Transactions\WebpayTransaction;
use PHPUnit\Framework\TestCase;

class WebpayAdapterTest extends TestCase
{

    /** @var WebpayAdapter */
    protected $adapter;

    /** @var AbstractClient&\Mockery\MockInterface */
    protected $client;

    /** @var array */
    protected $types = [
        'plus.normal',
        'plus.defer',
        'plus.mall.normal',
        'plus.mall.defer',
        'plus.capture',
        'plus.mall.capture',
        'plus.nullify',
        'plus.mall.nullify',
        'oneclick.register',
        'oneclick.confirm',
        'oneclick.unregister',
        'oneclick.charge',
        'oneclick.reverse',
    ];

    protected function setUp()
    {
        $this->adapter = new WebpayAdapter();

        $this->adapter->setCredentials(new Fluent([
            'foo' => 'bar'
        ]));

        $this->adapter->setIsProduction(true);

        $this->client = \Mockery::mock(AbstractClient::class, [
            true, new Fluent(['foo' => 'bar'])
        ]);

        $this->adapter->setClients([
            get_class($this->client) => $this->types
        ]);

        $this->adapter->setClient($this->client);
    }

    public function testRetrieveAndConfirm()
    {
        $transaction = new WebpayTransaction(['foo' => 'bar']);

        $this->client->allows([
            'retrieveAndConfirm' => true,
            'register' => true,
        ]);

        $retrievables = [
            'plus.normal',
            'plus.mall.normal',
            'oneclick.register',
        ];

        foreach ($retrievables as $retrievable) {
            $response = $this->adapter->retrieveAndConfirm($transaction, $retrievable);
            $this->assertTrue($response);
        }
    }

    public function testExceptionOnRetrieveAndConfirmUnavailable()
    {
        $this->expectException(ServiceSdkUnavailableException::class);
        $transaction = new WebpayTransaction(['foo' => 'bar']);
        $this->adapter->retrieveAndConfirm($transaction, 'any');
    }

    public function testRetrieve()
    {
        $transaction = new WebpayTransaction(['foo' => 'bar']);

        $this->client->allows([
            'retrieve' => true,
        ]);

        $retrievables = [
            'plus.normal',
            'plus.mall.normal',
        ];

        foreach ($retrievables as $retrievable) {
            $response = $this->adapter->retrieve($transaction, $retrievable);
            $this->assertTrue($response);
        }
    }

    public function testExceptionOnRetrieveUnavailable()
    {
        $this->expectException(ServiceSdkUnavailableException::class);

        $transaction = new WebpayTransaction(['foo' => 'bar']);
        $this->adapter->retrieve($transaction, 'any');
    }

    public function testCommit()
    {
        $this->client->allows([
            'commit' => true,
            'capture' => true,
            'nullify' => true,
            'register' => true,
            'confirm' => true,
            'unregister' => true,
            'charge' => true,
            'reverse' => true,
        ]);

        $types = [
            'plus.normal',
            'plus.defer',
            'plus.mall.normal',
            'plus.mall.defer',
            'plus.capture',
            'plus.mall.capture',
            'plus.nullify',
            'plus.mall.nullify',
            'oneclick.register',
            'oneclick.confirm',
            'oneclick.unregister',
            'oneclick.charge',
            'oneclick.reverse',
        ];

        foreach ($types as $type) {
            $transaction = new WebpayTransaction(['foo' => 'bar']);
            $transaction->setType($type);
            $response = $this->adapter->commit($transaction);
            $this->assertTrue($response);
        }
    }

    public function testExceptionOnCommitUnavailable()
    {
        $this->expectException(ServiceSdkUnavailableException::class);

        $transaction = new WebpayTransaction(['foo' => 'bar']);
        $transaction->setType('any');
        $this->adapter->commit($transaction);
    }

    public function testConfirm()
    {
        $transaction = new WebpayTransaction(['foo' => 'bar']);

        $this->client->allows([
            'confirm' => true,
        ]);

        $confirmables = [
            'plus.normal',
            'plus.mall.normal',
            'oneclick.confirm',
        ];

        foreach ($confirmables as $confirmable) {
            $response = $this->adapter->confirm($transaction, $confirmable);
            $this->assertTrue($response);
        }
    }

    public function testExceptionOnConfirmUnavailable()
    {
        $this->expectException(ServiceSdkUnavailableException::class);

        $this->client->allows([
            'confirm' => true,
        ]);

        $transaction = new WebpayTransaction(['foo' => 'bar']);

        $response = $this->adapter->confirm($transaction, 'anything.service');
    }
}
