<?php

namespace DependencyInjection;

use Database\InMemoryLogger;
use Database\MessageLoggerInterface;
use Queue\Adapter\InMemoryAdapter;

class TestContainerConfigurator extends ContainerConfigurator {

    /**
     * {@inheritDoc}
     */
    protected function createAdapter($queueName) {
        return new InMemoryAdapter();
    }

    /**
     * @param Container $container
     */
    protected function createLoggerService(Container $container) {
        $container[MessageLoggerInterface::class] = function ($container) {
            return new InMemoryLogger;
        };
    }
}
