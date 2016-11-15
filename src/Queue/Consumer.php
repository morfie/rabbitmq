<?php

namespace Queue;

use Queue\Adapter\AdapterInterface;
use Queue\ErrorHandler\AbstractErrorHandler;
use Queue\Exception\QueueProcessingException;
use Queue\Message\AbstractMessage;
use Queue\Message\MessageSerializer;
use Queue\Processor\AbstractProcessor;

class Consumer {

    /**
     * @var int
     */
    private $memoryLimit = 128;

    /**
     * @var int
     */
    private $processedMessage = 0;

    /**
     * @var int
     */
    private $maxProcessingMessage = 10000;

    /**
     * @var AbstractErrorHandler
     */
    private $errorHandler;

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var MessageSerializer
     */
    private $serializer;

    /**
     * @var AbstractProcessor
     */
    private $processor;

    /**
     * Consumer constructor.
     *
     * @param AdapterInterface     $adapter
     * @param AbstractProcessor    $processor
     * @param AbstractErrorHandler $errorHandler
     * @param MessageSerializer    $serializer
     */
    public function __construct(
        AdapterInterface $adapter, AbstractProcessor $processor,
        AbstractErrorHandler $errorHandler, MessageSerializer $serializer
    ) {
        $this->errorHandler = $errorHandler;
        $this->adapter = $adapter;
        $this->serializer = $serializer;
        $this->processor = $processor;
    }

    /**
     * process adapter messages
     */
    public function consume() {
        while (!$this->stopWorking()) {
            $this->adapter->consume(function ($rawMessage) {
                $this->doConsume($rawMessage);
                $this->processedMessage++;
            });
        }
    }

    /**
     * @param string $rawMessage
     */
    protected function doConsume($rawMessage) {
        $message = $this->serializer->unserialize($rawMessage);
        try {
            $this->process($message);
        } catch (\Exception $e) {
            $this->handleError($message, $e);
        }
    }

    /**
     * @return bool
     */
    protected function stopWorking() {
        return
            memory_get_usage(TRUE) >= $this->memoryLimit * 1024 * 1024 ||
            $this->processedMessage >= $this->maxProcessingMessage;
    }

    /**
     * @param AbstractMessage $message
     * @param \Exception      $e
     */
    protected function handleError(AbstractMessage $message, \Exception $e) {
        $this->errorHandler->handle(QueueProcessingException::create($message, $e));
    }

    /**
     * @param AbstractMessage $message
     */
    protected function process(AbstractMessage $message) {
        $this->processor->process($message);
    }
}