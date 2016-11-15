<?php

namespace Test\Queue\Processor;

use Queue\Message\AbstractMessage;
use Queue\Processor\AbstractProcessor;

class TestProcessor extends AbstractProcessor {

    /**
     * @var \Closure
     */
    private $innerProcessor;

    /**
     * TestProcessor constructor.
     *
     * @param \Closure $innerProcessor
     */
    public function __construct(\Closure $innerProcessor) {
        $this->innerProcessor = $innerProcessor;
    }

    /**
     * @param AbstractMessage $message
     */
    public function process(AbstractMessage $message) {
        $processor = $this->innerProcessor;
        $processor($message);
    }
}