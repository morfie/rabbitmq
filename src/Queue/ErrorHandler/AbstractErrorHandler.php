<?php

namespace Queue\ErrorHandler;

use Queue\Exception\ErrorHandlerNotFoundException;
use Queue\Exception\QueueProcessingException;

/**
 * abstract error handler that implement chain of responsibility
 */
abstract class AbstractErrorHandler {

    /**
     * @var self
     */
    protected $successor;

    /**
     * @param self $successor
     */
    public function setSuccessor(self $successor) {
        $this->successor = $successor;
    }

    /**
     * @param QueueProcessingException $exception
     */
    public function handle(QueueProcessingException $exception){

        if ($this->isSupported($exception)) {
            $this->doHandle($exception);
        } elseif ($this->successor !== null) {
            $this->successor->handle($exception);
        } else {
            throw ErrorHandlerNotFoundException::createFromException($exception);
        }
    }

    /**
     * @param QueueProcessingException $exception
     *
     * @return bool
     */
    abstract protected function isSupported(QueueProcessingException $exception);

    /**
     * @param QueueProcessingException $exception
     */
    abstract protected function doHandle(QueueProcessingException $exception);
}