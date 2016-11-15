<?php

namespace Http\Routing;

class Route {

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $action;

    /**
     * Route constructor.
     *
     * @param string $controller
     * @param string $action
     */
    public function __construct($controller, $action) {
        $this->controller = $controller;
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction() {
        return $this->action;
    }
}
