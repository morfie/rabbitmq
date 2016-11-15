<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

$container = new \DependencyInjection\Container();
$configurator = new \DependencyInjection\ContainerConfigurator();
$configurator->configure($container);

return $container;
