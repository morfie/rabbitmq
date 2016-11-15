#!/usr/bin/env php

<?php

use DependencyInjection\ContainerConfigurator;
use Queue\Consumer;

$container = require_once dirname(__DIR__).'/misc/bootstrap.php';

$consumerServiceId = ContainerConfigurator::generateQueueServiceId(
    ContainerConfigurator::QUEUE_NUMBER_HANDLER,
    Consumer::class
);
$consumer = $container[$consumerServiceId];
/** @var $consumer \Queue\Consumer */

$consumer->consume();
