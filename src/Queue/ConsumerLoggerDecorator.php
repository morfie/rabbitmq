<?php

namespace Queue;

use Database\MessageLoggerInterface;
use Queue\Message\AbstractMessage;

class ConsumerLoggerDecorator extends Consumer {

    /**
     * @var MessageLoggerInterface
     */
    private $logger;

    /**
     * @param MessageLoggerInterface $logger
     */
    public function setLogger(MessageLoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    protected function handleError(AbstractMessage $message, \Exception $e) {
        $this->logger->saveLogMessage($message->getId(), 'There was an error: '.$e->getMessage());
        parent::handleError($message, $e);
    }

    /**
     * {@inheritDoc}
     */
    protected function process(AbstractMessage $message) {
        $this->logger->saveLogMessage($message->getId(), 'Start processing...');
        parent::process($message);
        $this->logger->saveLogMessage($message->getId(), 'Finish processing!');
    }
}