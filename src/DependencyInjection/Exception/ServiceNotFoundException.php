<?php

namespace DependencyInjection\Exception;

class ServiceNotFoundException extends \LogicException {

    /**
     * @param $name
     *
     * @return ServiceNotFoundException
     */
    public static function createByName($name) {
        return new self(sprintf('"%s" service not found', $name));
    }
}