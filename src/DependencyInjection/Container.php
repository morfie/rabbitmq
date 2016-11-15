<?php

namespace DependencyInjection;

use DependencyInjection\Exception\ServiceNotFoundException;

/**
 * dependency injection container
 */
class Container implements \ArrayAccess {

    /**
     * @var array
     */
    private $prototypeList = [];

    /**
     * @var array
     */
    private $serviceList = [];

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset) {
        return isset($this->prototypeList[$offset]);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset) {
        if (!isset($this->prototypeList[$offset])) {
            throw ServiceNotFoundException::createByName($offset);
        }

        if (!isset($this->serviceList[$offset])) {
            $this->serviceList[$offset] = $this->prototypeList[$offset]($this);
        }

        return $this->serviceList[$offset];
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value) {
        $this->prototypeList[$offset] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset) {
        unset($this->prototypeList[$offset]);
        unset($this->serviceList[$offset]);
    }
}