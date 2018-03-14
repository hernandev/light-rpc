<?php

namespace LightRPC;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Response as HttpResponse;
use Mockery;

/**
 * Class ClientTest.
 *
 * Tests for the LightRPC client class.
 */
class ClientTest extends TestCase
{
    /**
     * @var string Dummy HTTP JSON-RPC server URL.
     */
    protected $server = 'http://some-json-rpc-server.com';

    /**
     * Dummy Http Response.
     *
     * @return HttpResponse
     */
    protected function getDummyResponse()
    {
        return new HttpResponse(
            200,
            ['content-type' => 'application/json'],
            json_encode([
                'id' => 1,
                'jsonrpc' => '2.0',
                'result' => [
                    'foo' => 'bar'
                ]
            ])
        );
    }

    /**
     * Test a new instance of the client could be created.
     */
    public function test_instance()
    {
        // start a client instance.
        $client = new Client($this->server);

        // assert the instance is of the correct type.
        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * Test customizing the http client instance if possible (getters and setters)
     */
    public function test_http_client_setter_and_getter()
    {
        // mock an http client.
        $httpClient = Mockery::mock(HttpClient::class);

        // start a new client.
        $client = new Client($this->server);

        // set the mock http client instance on the ROC client.
        $setReturn = $client->setHttpClient($httpClient);

        // assert the return was fluent, meaning the RPC client instance should be the same.
        $this->assertEquals($client, $setReturn);

        // assert the http client getter is returning the previously set instance.
        $this->assertEquals($client->getHttpClient(), $httpClient);
    }

    /**
     * Testing send using a Request instance.
     */
    public function test_request_instance_send()
    {
        $mockHttpClient = Mockery::mock(HttpClient::class);
        $mockHttpClient->shouldReceive('send')->andReturn($this->getDummyResponse());

        $client = new Client($this->server);
        $client->setHttpClient($mockHttpClient);

        $response = $client->send(new Request());

        $this->assertEquals(['foo' => 'bar'], $response->result());
    }

    /**
     * Simple raw parameter call test.
     */
    public function test_send_using_raw_parameters()
    {
        $mockHttpClient = Mockery::mock(HttpClient::class);
        $mockHttpClient->shouldReceive('send')->andReturn($this->getDummyResponse());

        $client = new Client($this->server);
        $client->setHttpClient($mockHttpClient);

        $response = $client->call('foo', 'bar', 'baz');

        $this->assertEquals(['foo' => 'bar'], $response->result());
    }

}