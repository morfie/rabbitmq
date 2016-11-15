<?php

namespace Queue\Adapter;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQAdapter implements AdapterInterface {

    /**
     * @var string
     */
    private $queueName;

    /**
     * @var string
     */
    private $exchange = 'amq.direct';

    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @param string $queueName
     */
    public function __construct($queueName) {
        $this->queueName = $queueName;
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
    }

    /**
     * {@inheritDoc}
     */
    public function consume(\Closure $processor) {

        $channel = $this->connection->channel();
        $innerProcessor = function (AMQPMessage $message) use ($processor) {
            $processor($message->getBody());
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        };

        $channel->basic_consume($this->queueName, spl_object_hash($this), true, false, false, false, $innerProcessor);

        $channel->wait();
        $channel->close();
    }

    /**
     * {@inheritDoc}
     */
    public function send($message) {
        $amqpMessage = $this->createAMQPMessage($message);
        $channel = $this->connection->channel();
        $channel->basic_publish($amqpMessage, $this->exchange, $this->queueName);
        $channel->close();
    }

    /**
     * intialize new queue
     */
    public function init() {
        $channel = $this->connection->channel();
        $channel->queue_declare($this->queueName, false, false, false, false);
        $channel->exchange_declare($this->exchange, 'direct', false, true, false);
        $channel->queue_bind($this->queueName, $this->exchange, $this->queueName);
        $channel->close();
    }

    /**
     * {@inheritDoc}
     */
    public function __destruct() {
        $this->connection->close();
    }

    /**
     * @param string $message
     *
     * @return AMQPMessage
     */
    protected function createAMQPMessage($message) {
        return new AMQPMessage(
            $message,
            [
                'content_type'  => 'text/plain',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );
    }
}