<?php

namespace Queue;

use Queue\Adapter\AdapterInterface;

class Initializer {

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Initializer constructor.
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    /**
     * init queue
     */
    public function setup() {
        $this->adapter->init();
    }
}