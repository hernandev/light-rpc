<?php

namespace LightRPC;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use JsonSerializable;

/**
 * Class Request.
 *
 * Generic request for JSON-RPC 2.0 calls.
 */
class Request implements Arrayable, JsonSerializable, Jsonable
{
    /**
     * @var int JSON-RPC call ID.
     */
    protected $id = 0;

    /**
     * @var string JSON-RPC method.
     */
    protected $method = 'call';

    /**
     * @var string JSON-RPC version.
     */
    protected $version = '2.0';

    /**
     * @var Collection JSON-RPC request parameters.
     */
    protected $params;

    /**
     * Request constructor.
     *
     * @param array ...$params
     */
    public function __construct(...$params)
    {
        $this->params = collect($params);
    }

    /**
     * Change the request ID.
     *
     * @param int $id Request ID to set.
     *
     * @return $this Fluent return.
     */
    public function setId(int $id = 0) : self
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Request ID getter.
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * JSON-RPC method setter.
     *
     * @param string $method
     *
     * @return $this
     */
    public function setMethod(string $method = 'call') : self
    {
        $this->method = (string) $method;

        return $this;
    }

    /**
     * JSON-RPC method getter.
     *
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * JSON-RPC version setter.
     *
     * @param string $version
     *
     * @return $this
     */
    public function setVersion(string $version = '2.0') : self
    {
        $this->version = (string) $version;

        return $this;
    }

    /**
     * JSON-RPC version getter.
     *
     * @return string
     */
    public function getVersion() : string
    {
        return $this->version;
    }

    /**
     * Transform the request into an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id'      => $this->id,
            'method'  => $this->method,
            'jsonrpc' => $this->version,
            'params'  => $this->params->toArray(),
        ];
    }

    /**
     * String representation of the request.
     *
     * @return string
     */
    public function __toString()
    {
        // make into array.
        $requestArray = $this->toArray();

        // encode as json.
        return json_encode($requestArray);
    }

    /**
     * JSON serialization shortcut.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return $this->__toString();
    }

    /**
     * JSON serialization shortcut.
     *
     * @return mixed|string
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
