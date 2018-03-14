<?php

namespace LightRPC;

use GuzzleHttp\Psr7\Response as HttpResponse;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * Class Response.
 *
 * Simple JSON-RPC response wrapper.
 */
class Response implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * @var HttpResponse Raw HTTP response.
     */
    protected $httpResponse;

    /**
     * @var bool Indicates network / transport errors.
     */
    protected $networkError = false;

    /**
     * @var bool Error / Success indicator
     */
    protected $isError = false;

    /**
     * @var null|int JSON-RPC response ID.
     */
    protected $id = null;

    /**
     * @var null|Collection Body values collection.
     */
    protected $body;

    /**
     * @var null|Collection Response data (result / error).
     */
    protected $data;

    /**
     * Response constructor.
     * @param HttpResponse $httpResponse
     */
    public function __construct(HttpResponse $httpResponse)
    {
        // init data as empty array.
        $this->data = collect([]);

        // init body as empty collection.
        $this->body = collect([]);

        // assign http response.
        $this->httpResponse = $httpResponse;

        // call the response parsing method.
        $this->parseResponse();
    }

    /**
     * Indicates if there's an error or not.
     * Error here is being considered also network errors.
     *
     * @return bool
     */
    public function isError() : bool
    {
        return $this->isError || $this->networkError;
    }

    /**
     * Magic data getter.
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->data->get($key, null);
    }

    /**
     * Retrieve a given key on the data result / error.
     *
     * @param string|null $key
     *
     * @return array|mixed
     */
    public function get(string $key = null)
    {
        if (!$key) {
            return $this->data->toArray();
        }

        return $this->data->get($key, null);
    }

    /**
     * Retrieve the data from the response.
     *
     * @return array
     */
    public function data() : array
    {
        return $this->data->toArray();
    }

    /**
     * Retrieve result, if not error.
     *
     * @return null|array
     */
    public function result() : ?array
    {
        if ($this->isError) {
            return null;
        }

        return $this->data->toArray();
    }

    /**
     * Retrieve error, if an error.
     *
     * @return null|array
     */
    public function error() : ?array
    {
        if (!$this->isError) {
            return null;
        }

        return $this->data->toArray();
    }

    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->body ? $this->body->toArray() : [];
    }

    /**
     * JSON serialization.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }

    /**
     * JSON serialization.
     *
     * @return mixed|string
     */
    public function jsonSerialize()
    {
        return $this->toJson();
    }

    /**
     * String serialization.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Parses the JSON-RPC HTTP response.
     *
     * @return null
     */
    protected function parseResponse()
    {
        // get the status code from response.
        $statusCode = $this->httpResponse->getStatusCode();

        // set as error, if http status code is not between 200 and 299 (inclusive).
        $this->networkError = !($statusCode >= 200 && $statusCode <= 299);

        // decode the response body.
        $body = $this->decodeBody();

        // stop when no body is present.
        if ($body) {
            // parse the response id from the body.
            $this->id = (int) array_get($body, 'id', null);

            // set as error when there is a an error key and it's not null.
            $this->isError = array_get($body, 'error', null) !== null;

            // parse the response data, from result or error.
            $this->data = collect(array_get($body, $this->isError ? 'error' : 'result', []));
        }


        // just return the response body.
        return $this->body;
    }

    /**
     * Returns the content type header, imploding with a 'comma' (,) when multiple.
     *
     * @return string
     */
    protected function getContentType() : string
    {
        return implode(',', (array) $this->httpResponse->getHeader('Content-Type'));
    }

    /**
     * Returns true if the Content-Type header simply contains the 'json' word.
     *
     * @return bool
     */
    protected function isJson()
    {
        $contentType = $this->getContentType();

        return Str::contains(Str::lower($contentType), 'json');
    }

    /**
     * Decode the response body as array.
     *
     * @return null|Collection
     */
    protected function decodeBody() : ?Collection
    {
        // avoid any parsing when there's not json header.
        if (!$this->isJson()) {
            return collect([]);
        }

        // parse the response as string.
        $body = (string) $this->httpResponse->getBody();

        // return the body, decoded as array.
        $this->body = collect(json_decode($body, true));

        // return the body value.
        return $this->body;
    }
}