<?php

$container = require_once dirname(__DIR__).'/misc/bootstrap.php';
$front = $container[\Http\Controller\FrontController::class];
/** @var $front \Http\Controller\FrontController */

$response = $front->dispatch(\Http\ValueObject\Request::createFromGlobals());
$response->send();
