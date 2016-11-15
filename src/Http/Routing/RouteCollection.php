<?php

namespace Http\Routing;

use Http\Exception\ActionNotFoundException;

class RouteCollection {

    /**
     * @var array
     */
    private $routeMap = [];

    /**
     * RouteCollection constructor.
     *
     * @param array $routeMap
     */
    public function __construct(array $routeMap) {
        $this->routeMap = $routeMap;
    }

    /**
     * @return Route
     */
    public function findRoute($action) {
        if (! isset($this->routeMap[$action])) {
            throw ActionNotFoundException::createByAction($action);
        }

        return $this->routeMap[$action];
    }
}
