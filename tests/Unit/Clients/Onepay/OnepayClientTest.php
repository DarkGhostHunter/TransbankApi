<?php

namespace Tests\Unit\Clients\Onepay;

use DarkGhostHunter\TransbankApi\Clients\Onepay\OnepayClient;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepayClientException;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepayResponseException;
use DarkGhostHunter\TransbankApi\Exceptions\Onepay\OnepayValidationException;
use DarkGhostHunter\TransbankApi\Helpers\Fluent;
use DarkGhostHunter\TransbankApi\Transactions\OnepayNullifyTransaction;
use DarkGhostHunter\TransbankApi\Transactions\OnepayTransaction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class OnepayClientTest extends TestCase
{

    /** @var OnepayClient */
    protected $client;

    protected function setUp()
    {
        $this->client = new OnepayClient(false, new Fluent([
            'appKey' => 'test-app',
            'apiKey' => 'test-key',
            'secret' => 'test-secret',
        ]));
    }

    public function testSetAndGetHttpClient()
    {
        $this->assertNull($this->client->getHttpClient());
        $this->client->setHttpClient(new Client());

        $this->assertInstanceOf(Client::class, $this->client->getHttpClient());
    }

    public function testBoot()
    {
        $this->assertNull($this->client->getEndpoint());
        $this->assertNull($this->client->getHttpClient());
        $this->client->boot();
        $this->assertIsString($this->client->getEndpoint());
        $this->assertInstanceOf(Client::class, $this->client->getHttpClient());
    }

    public function testCommit()
    {
        $httpClient = \Mockery::mock(Client::class);

        $httpClient->expects('post')
            ->with(
                'sendtransaction',
                ['body' => '{"foo":"bar","total":0,"itemsQuantity":0,"appKey":"test-app","apiKey":"test-key","signature":"GfWeI4CHVLeW1Sc7Iwvoxg/9ZMSY80/76dB7rcZZYnI=","externalUniqueNumber":null}']
            )
            ->andReturn(new Response(200, [], json_encode([
                'responseCode' => 'OK',
                'result' => [
                    'occ' => 'test-occ',
                    'externalUniqueNumber' => 'test-eun',
                    'issuedAt' => 'test-issuedAt',
                    'signature' => 'n8QMG1tvHMO+Kq/BnAY4uF777MRqrp9Z5Syx4d8aWlI='
                    ]
                ])
            ));

        $this->client->setHttpClient($httpClient);

        $transaction = new OnepayTransaction(['foo' => 'bar']);

        $response = $this->client->commit($transaction);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('occ', $response);
        $this->assertArrayHasKey('externalUniqueNumber', $response);
        $this->assertArrayHasKey('issuedAt', $response);
        $this->assertArrayHasKey('signature', $response);
    }

    public function testRefund()
    {
        $httpClient = \Mockery::mock(Client::class);

        $httpClient->expects('post')
            ->with(
                'nullifytransaction',
                ['body' => '{"foo":"bar","appKey":"test-app","apiKey":"test-key","signature":"UilLRCI7pG7EwEnFpLJLqvEAsOlUSKKh2sazMNZabd0="}']
            )
            ->andReturn(new Response(200, [], json_encode([
                    'responseCode' => 'OK',
                    'result' => [
                        'occ' => 'test-occ',
                        'externalUniqueNumber' => 'test-eun',
                        'issuedAt' => 'test-issuedAt',
                    ]
                ])
            ));

        $this->client->setHttpClient($httpClient);

        $transaction = new OnepayNullifyTransaction(['foo' => 'bar']);

        $response = $this->client->refund($transaction);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('occ', $response);
        $this->assertArrayHasKey('externalUniqueNumber', $response);
        $this->assertArrayHasKey('issuedAt', $response);
    }

    public function testConfirm()
    {
        $httpClient = \Mockery::mock(Client::class);

        $httpClient->expects('post')
            ->with(
                'gettransactionnumber',
                ['body' => '{"foo":"bar","appKey":"test-app","apiKey":"test-key","signature":"uvYGgjcgwlNDLLvw8W21iWl7FAtwtWh8P4+CYMaP5+Q=","externalUniqueNumber":null}']
            )
            ->andReturn(new Response(200, [], json_encode([
                    'responseCode' => 'OK',
                    'result' => [
                        'occ' => 'test-occ',
                        'externalUniqueNumber' => 'test-eun',
                        'authorizationCode' => 'test-auth',
                        'amount' => 9990,
                        'installmentsAmount' => 9990,
                        'installmentsNumber' => 1,
                        'buyOrder' => 'test-buyOrder',
                        'issuedAt' => 'test-issuedAt',
                        'signature' => 'fRM+RCYEM81CYiqLc1V61hYn3lb2HORYyjThaBwO1co='
                    ]
                ])
            ));

        $this->client->setHttpClient($httpClient);

        $transaction = new OnepayTransaction(['foo' => 'bar']);

        $response = $this->client->confirm($transaction);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('occ', $response);
        $this->assertArrayHasKey('externalUniqueNumber', $response);
        $this->assertArrayHasKey('authorizationCode', $response);
        $this->assertArrayHasKey('amount', $response);
        $this->assertArrayHasKey('installmentsAmount', $response);
        $this->assertArrayHasKey('installmentsNumber', $response);
        $this->assertArrayHasKey('buyOrder', $response);
        $this->assertArrayHasKey('issuedAt', $response);
        $this->assertArrayHasKey('signature', $response);
    }

    public function testInvalidSignature()
    {
        $this->expectException(OnepayValidationException::class);

        $httpClient = \Mockery::mock(Client::class);

        $httpClient->expects('post')
            ->with(
                'gettransactionnumber',
                ['body' => '{"foo":"bar","appKey":"test-app","apiKey":"test-key","signature":"uvYGgjcgwlNDLLvw8W21iWl7FAtwtWh8P4+CYMaP5+Q=","externalUniqueNumber":null}']
            )
            ->andReturn(new Response(200, [], json_encode([
                    'responseCode' => 'OK',
                    'result' => [
                        'occ' => 'test-occ',
                        'externalUniqueNumber' => 'test-eun',
                        'authorizationCode' => 'test-auth',
                        'amount' => 9990,
                        'installmentsAmount' => 9990,
                        'installmentsNumber' => 1,
                        'buyOrder' => 'test-buyOrder',
                        'issuedAt' => 'test-issuedAt',
                        'signature' => 'INVALIDSIGNATURE'
                    ]
                ])
            ));

        $this->client->setHttpClient($httpClient);

        $transaction = new OnepayTransaction(['foo' => 'bar']);

        $this->client->confirm($transaction);
    }

    public function testExceptionOnHttpClient()
    {
        $this->expectException(OnepayClientException::class);

        $httpClient = \Mockery::mock(Client::class);

        $httpClient->expects('post')
            ->andThrow(new RequestException("Error Communicating with Server", new Request('GET', 'test')));

        $this->client->setHttpClient($httpClient);

        $transaction = new OnepayTransaction(['foo' => 'bar']);

        $this->client->commit($transaction);
    }

    public function testExceptionOnMalformedResponse()
    {
        $this->expectException(OnepayResponseException::class);

        $httpClient = \Mockery::mock(Client::class);

        $httpClient->expects('post')
            ->andReturn(new Response(200, [], json_encode([
                    'responseCode' => 'INVALID_PARAMS',
                    'description' => 'Parametros invalidos'
                ]))
            );

        $this->client->setHttpClient($httpClient);

        $transaction = new OnepayTransaction(['foo' => 'bar']);

        $this->client->commit($transaction);

    }
}
