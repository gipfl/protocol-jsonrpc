<?php

namespace gipfl\Protocol\JsonRpc;

class Notification extends Packet
{
    /** @var string */
    protected $method;

    public function __construct($method, $params)
    {
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function toPlainObject()
    {
        $plain = [
            'jsonrpc' => '2.0',
            'method'  => $this->method,
            'params'  => $this->params,
        ];

        return $plain;
    }

    /**
     * @param $method
     * @param $params
     * @return static
     */
    public static function create($method, $params)
    {
        $packet = new static($method, (object) $params);

        return $packet;
    }
}
