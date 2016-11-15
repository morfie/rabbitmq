<?php

namespace Http\Controller;

use Http\Routing\RouteCollection;
use Http\ValueObject\Request;
use Http\ValueObject\Response;

class FrontController {

    /**
     * @var array
     */
    private $controllers = [];

    /**
     * @var RouteCollection
     */
    private $routeCollection;

    /**
     * FrontController constructor.
     *
     * @param RouteCollection $routeCollection
     * @param array           $controllers
     */
    public function __construct(RouteCollection $routeCollection, array $controllers) {
        $this->routeCollection = $routeCollection;
        $this->controllers = $controllers;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function dispatch(Request $request) {

        $action = $request->get('action', 'home');
        $route = $this->routeCollection->findRoute($action);
        $controller = $this->controllers[ $route->getController() ];

        return call_user_func([ $controller, $route->getAction() ], $request);
    }
}