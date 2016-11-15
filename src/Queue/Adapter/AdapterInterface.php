<?php
namespace Queue\Adapter;

interface AdapterInterface {

    /**
     * @param string $message
     */
    public function send($message);

    /**
     * intialize new queue
     */
    public function init();

    /**
     * @param \Closure $processor
     */
    public function consume(\Closure $processor);
}