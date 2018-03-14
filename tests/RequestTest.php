<?php

namespace LightRPC;

use PHPUnit\Framework\TestCase;

/**
 * Class RequestTest.
 *
 * Tests for the LightRPC client Request class.
 */
class RequestTest extends TestCase
{
    /**
     * Test Request ID setter and getter.
     */
    public function test_id_setter_and_getter()
    {
        // start instance.
        $request = new Request();

        // customize the ID.
        $returned = $request->setId(9);

        // assert the return was fluent.
        $this->assertEquals($request, $returned);

        // assert the value was indeed set, also, test the getter.
        $this->assertEquals(9, $request->getId());
    }

    /**
     * Test Request method setter and getter.
     */
    public function test_method_setter_and_getter()
    {
        // start instance.
        $request = new Request();

        // customize the default method value.
        $returned = $request->setMethod('ring-ha-ha-ha');

        // test the fluent setter return.
        $this->assertEquals($request, $returned);

        // assert the method getter and the setter value previously used.
        $this->assertEquals('ring-ha-ha-ha', $request->getMethod());
    }

    /**
     * Test Request version setter and getter.
     */
    public function test_version_setter_and_getter()
    {
        // start instance.
        $request = new Request();

        // customize the default JSON-RPC version.
        $returned = $request->setVersion('9.0');

        // test the fluent setter return.
        $this->assertEquals($request, $returned);

        // assert the method getter and the setter value previously used.
        $this->assertEquals('9.0', $request->getVersion());
    }

    /**
     * Test request constructor.
     */
    public function test_parameter_expandable_constructor()
    {
        // start a request with custom values.
        $request = new Request('foo', 'bar', ['baz']);

        // arrayable versions of params must match.
        $this->assertEquals(['foo', 'bar', ['baz']], $request->toArray()['params']);
    }

    /**
     * Test request JSON serialization.
     */
    public function test_json_string_serialization()
    {
        $fullRequestBody = [
            'id' => 0,
            'method' => 'call',
            'jsonrpc' => '2.0',
            'params' => [
                'foo',
                'bar',
                [ 'baz' ]
            ]
        ];

        $request = new Request(...$fullRequestBody['params']);

        $this->assertEquals(json_encode($fullRequestBody), (string) $request);
        $this->assertEquals(json_encode($fullRequestBody), $request->toJson());
        $this->assertEquals(json_encode($fullRequestBody), $request->jsonSerialize());
    }

}