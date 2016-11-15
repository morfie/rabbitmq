<?php

namespace Database;

interface MessageLoggerInterface {

    /**
     * @param string $correlationId
     * @param string $message
     */
    public function saveLogMessage($correlationId, $message);

    /**
     * @param string $correlationId
     *
     * @return \Traversable
     */
    public function findMessageByCorrelationId($correlationId);
}