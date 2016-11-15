<?php

namespace Queue\Exception;

use Queue\Message\AbstractMessage;

class QueueProcessingException extends \Exception {

    /**
     * @var AbstractMessage
     */
    private $queueMessage;

    /**
     * {@inheritDoc}
     */
    public function __construct(AbstractMessage $queueMessage, $message = "", $code = 0, \Exception $previous = null) {
        $this->queueMessage = $queueMessage;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param AbstractMessage $message
     * @param \Exception      $exception
     *
     * @return QueueProcessingException
     */
    public static function create(AbstractMessage $message, \Exception $exception) {
        return new self($message, $exception->getMessage(), $exception->getCode(), $exception);
    }

    /**
     * @return AbstractMessage
     */
    public function getQueueMessage() {
        return $this->queueMessage;
    }
}
