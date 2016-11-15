<?php

namespace Queue\Message;

class MailMessage extends AbstractMessage {

    /**
     * @var int
     */
    protected $number;

    /**
     * @return int
     */
    public function getNumber() {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber($number) {
        $this->number = $number;
    }
}
