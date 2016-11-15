<?php

namespace Http\Exception;

class ActionNotFoundException extends \Exception {

    /**
     * @param $action
     *
     * @return ActionNotFoundException
     */
    public static function createByAction($action) {
        return new self(sprintf('"%s" not found in routing map', $action));
    }
}