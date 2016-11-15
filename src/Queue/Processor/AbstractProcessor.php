<?php

namespace Queue\Processor;

use Queue\Message\AbstractMessage;

abstract class AbstractProcessor {

    /**
     * @param AbstractMessage $message
     */
    abstract public function process(AbstractMessage $message);
}