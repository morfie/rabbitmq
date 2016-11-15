<?php

namespace Queue\Adapter;

class InMemoryAdapter implements AdapterInterface {

    /**
     * @var array
     */
    private $queue;

    /**
     * {@inheritDoc}
     */
    public function send($message) {
        $this->queue[] = $message;
    }

    /**
     * {@inheritDoc}
     */
    public function init() {
        // nothing
    }

    /**
     * {@inheritDoc}
     */
    public function consume(\Closure $processor) {
        $firstElement = array_shift($this->queue);
        if (!$firstElement) {
            throw new \UnderflowException('Queue is empty!');
        }
        $processor($firstElement);
    }

    /**
     * @return array
     */
    public function getQueue() {
        return $this->queue;
    }
}