<?php

namespace Database;

class LogDbGateway implements MessageLoggerInterface {

    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @param \PDO $connection
     */
    public function __construct(\PDO $connection) {
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function saveLogMessage($correlationId, $message) {
        $stmt = $this->connection->prepare(
            'INSERT INTO log (correlationId, message) VALUES (:correlationId, :message)'
        );
        $stmt->execute([
            ':correlationId' => $correlationId,
            ':message' => $message,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function findMessageByCorrelationId($correlationId) {
        $stmt = $this->connection->prepare('SELECT message FROM log where correlationId = :id');
        $stmt->execute([
            ':id' => $correlationId,
        ]);

        return $stmt;
    }

    /**
     * init db
     */
    public function setup() {
        $sql = '
            CREATE TABLE log
            (
              id INT PRIMARY KEY AUTO_INCREMENT,
              correlationId VARCHAR(15),
              message VARCHAR(255)
            );
            CREATE INDEX correlationId_idx ON log (correlationId);
        ';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
    }
}