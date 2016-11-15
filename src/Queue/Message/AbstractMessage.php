<?php

namespace Queue\Message;

abstract class AbstractMessage {

    /**
     * @var int
     */
    protected $failCounter = 0;

    /**
     * @var string
     */
    protected $id;

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getFailCounter() {
        return $this->failCounter;
    }

    /**
     * @return void
     */
    public function increaseFailCounter() {
        $this->failCounter++;
    }
}
