<?php

namespace Queue\Processor;

use Queue\Message\AbstractMessage;

class NumberHandlerProcessor extends AbstractProcessor {

    /**
     * {@inheritDoc}
     */
    public function process(AbstractMessage $message) {
        $result = random_int(1, 3);
        if ($result != 1) {
            throw new \DomainException(sprintf('Message processing failed, because %d!=1 !', $result));
        }
    }
}