<?php

namespace Queue\Processor;

use Queue\Message\AbstractMessage;
use Queue\Message\MailMessage;

class MailProcessor extends AbstractProcessor {

    /**
     * {@inheritDoc}
     */
    public function process(AbstractMessage $message) {
        /** @var $message MailMessage */

        printf("SEND MAIL number(%s)...\n", $message->getNumber());
    }
}
