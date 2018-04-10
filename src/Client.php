<?php

namespace LightRPC;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Psr7\Request as HttpRequest;
use GuzzleHttp\Psr7\Response as HttpResponse;

/**
 * Class Client.
 *
 * Simple JSON-RPC 2.0 client.
 *
 * This library is intended to be simple, so as far as transport goes,
 * HTTP(S) will be the only implementation for now.
 */
class Client
{
    /**
     * @var null|string JSON-RPC server URL.
     */
    protected $server = null;

    /**
     * @var array Default list of headers.
     */
    protected $headers = [
        'Accept'       => 'application/json',
        'Content-Type' => 'application/json',
    ];

    protected $httpClient;

    /**
     * Client constructor.
     *
     * @param string $serverUrl
     * @param array  $headers   List of default headers.
     */
    public function __construct(string $serverUrl, array $headers = [])
    {
        // setup the http client instance.
        $this->httpClient = new HttpClient();

        // assign server version on the client instance.
        $this->server = $serverUrl;

        // merge default headers with new headers.
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * @param array ...$parameters
     *
     * @return Response
     */
    public function call(...$parameters)
    {
        // factory a request instance from given parameters.
        $request = new Request(...$parameters);

        // send the just created request.
        return $this->send($request);
    }

    /**
     * Sends a Request instance to the JSON-RPC server.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function send(Request $request)
    {
        $httpResponse = $this->sendHttpRequest($request->toArray());

        return new Response($httpResponse);
    }

    /**
     * Sends the HTTP requests to the JSON-RPC server.
     *
     * @param array|string $body
     * @param string       $method
     *
     * @return HttpResponse
     */
    protected function sendHttpRequest($body, string $method = 'post') : HttpResponse
    {
        // parse the body as json string, if the input value was an array.
        $jsonBody = is_array($body) ? json_encode($body) : $body;

        // creates a new http request, from the provided data.
        $request = new HttpRequest($method, $this->server, $this->headers, $jsonBody);

        // sends the request and return the response.
        return $this->httpClient->send($request);
    }

    /**
     * Retrieve the HTTP client instance.
     *
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * Customize the HTTP client instance.
     *
     * @param HttpClient $httpClient
     *
     * @return $this
     */
    public function setHttpClient(HttpClient $httpClient) : self
    {
        $this->httpClient = $httpClient;

        // fluent return.
        return $this;
    }
}
