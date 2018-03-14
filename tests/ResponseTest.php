<?php

namespace LightRPC;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response as HttpResponse;

/**
 * Class ResponseTest.
 *
 * Tests for the LightRPC client Response class.
 */
class ResponseTest extends TestCase
{

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
     * Dummy Http Response [ERROR].
     *
     * @return HttpResponse
     */
    protected function getDummyErrorResponse()
    {
        return new HttpResponse(
            200,
            ['content-type' => 'application/json'],
            json_encode([
                'id' => 1,
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => 'bar'
                ]
            ])
        );
    }

    /**
     * Dummy Http Response [ERROR].
     *
     * @return HttpResponse
     */
    protected function getNonJsonResponse()
    {
        return new HttpResponse(
            200,
            ['content-type' => 'application/xml'],
            '<?xml version="1.0" encoding="UTF-8"?><root><bar>foo</bar><foo>bar</foo></root>'
        );
    }

    /**
     * Test a correct response parsing.
     */
    public function test_result_response_parsing()
    {
        // create a result response (fake).
        $response = new Response($this->getDummyResponse());

        // assert is no error.
        $this->assertFalse($response->isError());

        // assert magic getter.
        $this->assertEquals($response->foo, 'bar');
        // assert normal getter.
        $this->assertEquals($response->get('foo'), 'bar');
        // assert result getter (no arguments).
        $this->assertEquals($response->get(), ['foo' => 'bar']);
        // assert data retrieval (full data).
        $this->assertEquals($response->data(), ['foo' => 'bar']);
        // assert result key.
        $this->assertEquals($response->result(), ['foo' => 'bar']);
        // assert error is null.
        $this->assertNull($response->error());
    }

    /**
     * Test a error response parsing.
     */
    public function test_error_response_parsing()
    {
        // create a result response (fake ERROR).
        $response = new Response($this->getDummyErrorResponse());

        // assert is no error.
        $this->assertTrue($response->isError());

        // assert magic getter.
        $this->assertEquals($response->code, 'bar');
        // assert normal getter.
        $this->assertEquals($response->get('code'), 'bar');
        // assert error getter (no arguments).
        $this->assertEquals($response->get(), ['code' => 'bar']);
        // assert data retrieval (full data).
        $this->assertEquals($response->data(), ['code' => 'bar']);
        // assert error key.
        $this->assertEquals($response->error(), ['code' => 'bar']);
        // assert result is null.
        $this->assertNull($response->result());
    }

    /**
     * Test for JSON and string serialization / parsing
     */
    public function test_json_parsing()
    {
        // some fake data.
        $responseData = [
            'id' => 1,
            'jsonrpc' => '2.0',
            'error' => [
                'code' => 'bar'
            ]
        ];

        // start response.
        $response = new Response($this->getDummyErrorResponse());

        // force string serialization.
        $this->assertEquals(json_encode($responseData), (string) $response);
        // array representation.
        $this->assertEquals($responseData, $response->toArray());
        // json serialization method 1.
        $this->assertEquals(json_encode($responseData), $response->toJson());
        // json serialization method 2.
        $this->assertEquals(json_encode($responseData), $response->jsonSerialize());
    }

    public function test_non_json_response_parsing()
    {
        // start a response instance without an actual JSON body.
        $response = new Response($this->getNonJsonResponse());

        // assert an empty response when representing as array.
        $this->assertEquals([], $response->toArray());
    }

}