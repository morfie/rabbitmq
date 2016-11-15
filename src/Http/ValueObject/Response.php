<?php

namespace Http\ValueObject;

class Response {

    /**
     * @var string
     */
    private $content;

    /**
     * Response constructor.
     *
     * @param string $content
     */
    public function __construct($content) {
        $this->content = $content;
    }

    /**
     * @param string $content
     *
     * @return Response
     */
    public static function create($content = '') {
        return new static($content);
    }

    /**
     * send response content to output
     */
    public function send() {
        echo $this->content;
    }
}