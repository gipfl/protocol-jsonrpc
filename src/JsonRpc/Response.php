<?php

namespace gipfl\Protocol\JsonRpc;

class Response extends Packet
{
    /** @var string $id */
    protected $id;

    /** @var mixed */
    protected $result;

    /** @var string */
    protected $error;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public static function forRequest(Request $request)
    {
        $response = new Response($request->getId());
        return $response;
    }

    /**
     * @return object
     */
    public function toPlainObject()
    {
        $plain = (object) [
            'jsonrpc' => '2.0',
            'params'  => $this->params,
        ];

        if ($this->id !== null) {
            $plain->id = $this->id;
        }

        return $plain;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return bool
     */
    public function hasId()
    {
        return null !== $this->id;
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }
}
