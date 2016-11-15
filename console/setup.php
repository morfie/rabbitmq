#!/usr/bin/env php

<?php

use DependencyInjection\ContainerConfigurator;
use Queue\Initializer;

$container = require_once dirname(__DIR__).'/misc/bootstrap.php';

foreach ([ContainerConfigurator::QUEUE_MAILER, ContainerConfigurator::QUEUE_NUMBER_HANDLER] as $queueName) {
    $initializerId = ContainerConfigurator::generateQueueServiceId(
        $queueName,
        Initializer::class
    );
    $initializer = $container[$initializerId];
    /** @var $initializer \Queue\Initializer */
    $initializer->setup();
}

$db = $container[\Database\MessageLoggerInterface::class];
/** @var $db \Database\LogDbGateway */
$db->setup();
