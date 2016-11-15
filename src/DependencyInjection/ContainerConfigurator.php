<?php

namespace DependencyInjection;

use Database\LogDbGateway;
use Database\MessageLoggerInterface;
use Http\Controller\FrontController;
use Http\Controller\MainController;
use Http\Rendering\JsonRendering;
use Http\Rendering\PhpRendering;
use Http\Routing\RouteCollection;
use Http\Routing\Route;
use Queue\Adapter\RabbitMQAdapter;
use Queue\Consumer;
use Queue\ConsumerLoggerDecorator;
use Queue\ErrorHandler\MailErrorHandler;
use Queue\ErrorHandler\ThreeAttemptErrorHandler;
use Queue\Initializer;
use Queue\Message\AbstractMessage;
use Queue\Message\MailMessage;
use Queue\Message\MessageSerializer;
use Queue\Message\NumberHandlerMessage;
use Queue\Processor\AbstractProcessor;
use Queue\Processor\MailProcessor;
use Queue\Processor\NumberHandlerProcessor;
use Queue\Processor\Processor;
use Queue\Publisher;

/**
 * a serice container configurator
 */
class ContainerConfigurator {

    const QUEUE_NUMBER_HANDLER = 'number-generator-queue';
    const QUEUE_MAILER = 'mailer-queue';

    /**
     * @param Container $container
     */
    public function configure(Container $container) {

        $container[MainController::class] = function ($container) {
            return new MainController(
                $container[PhpRendering::class],
                $container[JsonRendering::class],
                $container[self::generateQueueServiceId(self::QUEUE_NUMBER_HANDLER, Publisher::class)],
                $container[MessageLoggerInterface::class]
            );
        };

        $container[FrontController::class] = function ($container) {
            $controllers[MainController::class] = $container[MainController::class];
            return new FrontController($container[RouteCollection::class], $controllers);
        };

        $container[PhpRendering::class] = function ($container) {
            return new PhpRendering();
        };

        $container[JsonRendering::class] = function ($container) {
            return new JsonRendering();
        };

        $container[MailErrorHandler::class] = function ($container) {
            return new MailErrorHandler(
                $container[self::generateQueueServiceId(self::QUEUE_MAILER, Publisher::class)],
                $container[MessageLoggerInterface::class]
            );
        };

        $container[ThreeAttemptErrorHandler::class] = function ($container) {
            $handler = new ThreeAttemptErrorHandler(
                $container[self::generateQueueServiceId(self::QUEUE_NUMBER_HANDLER, Publisher::class)]
            );
            $handler->setSuccessor($container[MailErrorHandler::class]);
            return $handler;
        };

        $container[RouteCollection::class] = function ($container) {
            return new RouteCollection([
                'home' => new Route(MainController::class, 'homeAction'),
                'generate' => new Route(MainController::class, 'generateAction'),
                'info' => new Route(MainController::class, 'readInfoAction'),
            ]);
        };

        $container[MessageSerializer::class] = function ($container) {
            return new MessageSerializer();
        };

        $container[\PDO::class] = function ($container) {
            return new \PDO('mysql:host=127.0.0.1;dbname=logs', 'test', 'test');
        };

        $this->createLoggerService($container);

        $this->createQueuePackage(
            $container,
            self::QUEUE_NUMBER_HANDLER,
            new NumberHandlerMessage,
            new NumberHandlerProcessor
        );

        $this->createQueuePackage(
            $container,
            self::QUEUE_MAILER,
            new MailMessage,
            new MailProcessor
        );
    }

    /**
     * @param Container         $container
     * @param string            $queueName
     * @param AbstractMessage   $message
     * @param AbstractProcessor $processor
     */
    protected function createQueuePackage(
        Container $container, $queueName, AbstractMessage $message, AbstractProcessor $processor
    ) {

        $adapter = $this->createAdapter($queueName);

        $container[self::generateQueueServiceId($queueName, Publisher::class)] = function ($container) use ($adapter, $message, $processor) {
            return new Publisher($message, $adapter, $container[MessageSerializer::class]);
        };

        $container[self::generateQueueServiceId($queueName, Initializer::class)] = function ($container) use ($adapter) {
            return new Initializer($adapter);
        };

        $container[self::generateQueueServiceId($queueName, Consumer::class)] = function ($container) use ($adapter, $message, $processor) {
            $consumer = new ConsumerLoggerDecorator(
                $adapter,
                $processor,
                $container[ThreeAttemptErrorHandler::class],
                $container[MessageSerializer::class]
            );
            $consumer->setLogger($container[MessageLoggerInterface::class]);
            return $consumer;
        };
    }

    /**
     * @param $queueName
     * @param $service
     *
     * @return string
     */
    public static function generateQueueServiceId($queueName, $service) {
        return $queueName . '-' . $service;
    }

    /**
     * @param $queueName
     *
     * @return RabbitMQAdapter
     */
    protected function createAdapter($queueName) {
        return new RabbitMQAdapter($queueName);
    }

    /**
     * @param Container $container
     */
    protected function createLoggerService(Container $container) {
        $container[MessageLoggerInterface::class] = function ($container) {
            return new LogDbGateway($container[\PDO::class]);
        };
    }
}