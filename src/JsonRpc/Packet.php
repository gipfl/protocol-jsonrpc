<?php

namespace gipfl\Protocol\JsonRpc;

use gipfl\Protocol\JsonRpc\Exception\ProtocolError;

abstract class Packet
{
    /** @var object */
    protected $params;

    abstract public function toPlainObject();

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @param $name
     * @param mixed $default
     * @return mixed|null
     */
    public function getParam($name, $default = null)
    {
        $p = & $this->params;
        if (\is_object($p) && \property_exists($p, $name)) {
            return $p->$name;
        } elseif (\is_array($p) && \array_key_exists($name, $p)) {
            return $p[$name];
        }

        return $default;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return \json_encode($this->toPlainObject());
    }

    /**
     * @return string
     */
    public function toPrettyString()
    {
        return \json_encode($this->toPlainObject(), JSON_PRETTY_PRINT);
    }

    /**
     * @param $string
     * @return Notification|Request|Response
     * @throws ProtocolError
     */
    public static function decode($string)
    {
        $raw = \json_decode($string);
        if (null === $raw) {
            throw new ProtocolError(
                'JSON decode failed: %s',
                \json_last_error_msg()
            );
        }
        static::assertPropertyExists($raw, 'jsonrpc');

        if ($raw->jsonrpc !== '2.0') {
            throw new ProtocolError(
                'Only JSON-RPC 2.0 is supported, got %s',
                $raw->jsonrpc
            );
        }

        if (\property_exists($raw, 'method')) {
            static::assertPropertyExists($raw, 'params');
            if (\property_exists($raw, 'id')) {
                return new Request($raw->method, $raw->params, $raw->id);
            } else {
                return new Notification($raw->method, $raw->params);
            }
        } elseif (\property_exists($raw, 'id')) {
            $packet = new Response($raw->id);
        } else {
            throw new ProtocolError(
                'Given string is not a valid JSON-RPC 2.0 packet: %s',
                $string
            );
        }

        return $packet;
    }

    /**
     * @param $object
     * @param $property
     * @throws ProtocolError
     */
    protected static function assertPropertyExists($object, $property)
    {
        if (! \property_exists($object, $property)) {
            throw new ProtocolError(
                'Expected valid JSON-RPC, got no %d property',
                $property
            );
        }
    }
}
