<?php

namespace Queue\Message;

class MessageSerializer {

    /**
     * @param AbstractMessage $message
     *
     * @return string
     */
    public function serialize(AbstractMessage $message) {
        return serialize($message);
    }

    /**
     * @param string $value
     *
     * @return AbstractMessage
     */
    public function unserialize($value) {
        return unserialize($value);
    }
}