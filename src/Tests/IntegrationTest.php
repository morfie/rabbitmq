<?php

namespace Tests;

use Database\MessageLoggerInterface;
use DependencyInjection\Container;
use DependencyInjection\ContainerConfigurator;
use DependencyInjection\TestContainerConfigurator;
use Queue\Consumer;
use Queue\Message\AbstractMessage;
use Queue\Message\NumberHandlerMessage;
use Queue\Publisher;
use Test\Queue\Processor\TestProcessor;

class IntegrationTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Container
     */
    private static $container;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        static::$container = new Container();
        $configurator = new TestContainerConfigurator();
        $configurator->configure(static::$container);
    }

    /**
     * Successful at first time
     */
    public function testSuccessFirstTime() {
        $message = $this->sendMessage();

        $processor = function (AbstractMessage $message) {
            // nothing
        };
        $this->consumeQueue($processor, 1);

        $logs = $this->getLogs($message);
        $this->assertEquals(['Start processing...', 'Finish processing!'], $logs);
    }

    /**
     * Third attempt will be successful
     */
    public function testSuccessThirdTime() {
        $message = $this->sendMessage();

        $counter = 1;
        $processor = function (AbstractMessage $message) use (&$counter) {
            if ($counter++ < 3) {
                throw new \Exception('DB error');
            }
        };
        $this->consumeQueue($processor, 3);

        $logs = $this->getLogs($message);
        $this->assertEquals([
            'Start processing...',
            'There was an error: DB error',
            'Start processing...',
            'There was an error: DB error',
            'Start processing...',
            'Finish processing!',
        ], $logs);
    }

    /**
     * Third attempt will be fail too
     */
    public function testFailThirdTime() {
        $message = $this->sendMessage();

        $processor = function (AbstractMessage $message) {
            throw new \Exception('DB error');
        };
        $this->consumeQueue($processor, 3);

        $logs = $this->getLogs($message);
        $this->assertEquals([
            'Start processing...',
            'There was an error: DB error',
            'Start processing...',
            'There was an error: DB error',
            'Start processing...',
            'There was an error: DB error',
            'We didnt process this message 3 times, then we send an mail...',
        ], $logs);
    }

    /**
     * @return Publisher
     */
    private function getPublisher() {
        return static::$container[ContainerConfigurator::generateQueueServiceId(
            ContainerConfigurator::QUEUE_NUMBER_HANDLER, Publisher::class
        )];
    }

    /**
     * @return Consumer
     */
    private function getConsumer() {
        return static::$container[ContainerConfigurator::generateQueueServiceId(
            ContainerConfigurator::QUEUE_NUMBER_HANDLER, Consumer::class
        )];
    }

    /**
     * @param mixed $object
     * @param string $name
     * @param string $value
     */
    private function propertySetter($object, $name, $value) {
        $reflection = new \ReflectionObject($object);
        while( !$reflection->hasProperty($name) ){
            $reflection = $reflection->getParentClass();
        }
        $reflectionProperty = $reflection->getProperty($name);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    /**
     * @return MessageLoggerInterface
     */
    protected function getLogger() {
        return static::$container[MessageLoggerInterface::class];
    }

    /**
     * @return AbstractMessage|NumberHandlerMessage
     */
    protected function sendMessage() {
        $message = $this->getPublisher()->createMessage();
        $message->setNumber(100);

        /** @var $message NumberHandlerMessage */
        $this->getPublisher()->send($message);

        return $message;
    }

    /**
     * @param \Closure $processor
     * @param int      $maxProcessing
     */
    protected function consumeQueue(\Closure $processor, $maxProcessing) {
        $consumer = $this->getConsumer();
        $this->propertySetter($consumer, 'processor', new TestProcessor($processor));
        $this->propertySetter($consumer, 'maxProcessingMessage', $maxProcessing);
        $consumer->consume();
    }

    /**
     * @param $message
     *
     * @return array
     */
    protected function getLogs($message) {
        $logs = $this->getLogger()->findMessageByCorrelationId($message->getId());
        return iterator_to_array($logs);
    }
}
