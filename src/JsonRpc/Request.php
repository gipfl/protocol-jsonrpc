<?php

namespace gipfl\Protocol\JsonRpc;

use gipfl\Protocol\JsonRpc\Exception\ProtocolError;

class Request extends Notification
{
    protected $id;

    public function __construct($method, $params = null, $id = null)
    {
        parent::__construct($method, $params);
        $this->id = $id;
    }

    /**
     * @return array
     * @throws ProtocolError
     */
    public function toPlainObject()
    {
        if ($this->id === null) {
            throw new ProtocolError(
                'A request without an ID is not valid'
            );
        }

        $plain = parent::toPlainObject();
        $plain['id'] = $this->id;

        return $plain;
    }

    /**
     * @return bool
     */
    public function hasId()
    {
        return null !== $this->id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
