<?php

namespace Queue\ErrorHandler;

use Queue\Exception\QueueProcessingException;
use Queue\Exception\ResendMessageException;
use Queue\Publisher;

class ThreeAttemptErrorHandler extends AbstractErrorHandler {

    /**
     * @var Publisher
     */
    private $publisher;

    /**
     * ThreeAttemptErrorHandler constructor.
     *
     * @param Publisher $publisher
     */
    public function __construct(Publisher $publisher) {
        $this->publisher = $publisher;
    }

    /**
     * {@inheritDoc}
     */
    protected function isSupported(QueueProcessingException $exception) {
        return $exception->getQueueMessage()->getFailCounter() < 2;
    }

    /**
     * {@inheritDoc}
     */
    protected function doHandle(QueueProcessingException $exception) {
        $this->publisher->resend($exception->getQueueMessage());
    }
}