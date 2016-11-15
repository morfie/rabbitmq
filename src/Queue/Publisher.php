<?php

namespace Queue;

use Queue\Adapter\AdapterInterface;
use Queue\Message\AbstractMessage;
use Queue\Message\MessageSerializer;

class Publisher {

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * @var MessageSerializer
     */
    private $serializer;

    /**
     * @var AbstractMessage
     */
    private $messagePrototype;

    /**
     * Publisher constructor.
     *
     * @param AbstractMessage   $message
     * @param AdapterInterface  $adapter
     * @param MessageSerializer $serializer
     */
    public function __construct(AbstractMessage $message, AdapterInterface $adapter, MessageSerializer $serializer) {
        $this->adapter = $adapter;
        $this->serializer = $serializer;
        $this->messagePrototype = $message;
    }

    /**
     * @return AbstractMessage
     */
    public function createMessage() {
        return clone $this->messagePrototype;
    }

    /**
     * @param AbstractMessage $message
     */
    public function send(AbstractMessage $message) {
        $message->setId(uniqid());
        $this->adapter->send($this->serializer->serialize($message));
    }

    /**
     * @param AbstractMessage $message
     */
    public function resend(AbstractMessage $message) {
        $message->increaseFailCounter();
        $this->adapter->send($this->serializer->serialize($message));
    }
}