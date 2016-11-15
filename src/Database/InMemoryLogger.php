<?php

namespace Database;

class InMemoryLogger implements MessageLoggerInterface {

    /**
     * @var array
     */
    private $messages = [];

    /**
     * {@inheritDoc}
     */
    public function saveLogMessage($correlationId, $message) {
        $this->messages[$correlationId][] = $message;
    }

    /**
     * {@inheritDoc}
     */
    public function findMessageByCorrelationId($correlationId) {
        if (!isset($this->messages[$correlationId])) {
            return new \ArrayIterator([]);
        }
        return new \ArrayIterator($this->messages[$correlationId]);
    }
}