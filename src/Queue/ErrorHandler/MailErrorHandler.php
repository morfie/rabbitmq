<?php

namespace Queue\ErrorHandler;

use Database\MessageLoggerInterface;
use Queue\Exception\QueueProcessingException;
use Queue\Message\MailMessage;
use Queue\Publisher;

class MailErrorHandler extends AbstractErrorHandler {

    /**
     * @var MessageLoggerInterface
     */
    private $logger;

    /**
     * @var Publisher
     */
    private $publisher;

    /**
     * MailErrorHandler constructor.
     *
     * @param Publisher    $publisher
     * @param MessageLoggerInterface $logger
     */
    public function __construct(Publisher $publisher, MessageLoggerInterface $logger) {
        $this->logger = $logger;
        $this->publisher = $publisher;
    }

    /**
     * {@inheritDoc}
     */
    protected function isSupported(QueueProcessingException $exception) {
        return TRUE;
    }

    /**
     * {@inheritDoc}
     */
    protected function doHandle(QueueProcessingException $exception) {

        // add message to mailer queue
        $message = $this->publisher->createMessage();
        /** @var $message MailMessage */
        $message->setNumber($exception->getQueueMessage()->getNumber());
        $this->publisher->send($message);

        $this->logger->saveLogMessage(
            $exception->getQueueMessage()->getId(),
            'We didnt process this message 3 times, then we send an mail...'
        );
    }
}