<?php

namespace Http\ValueObject;

/**
 * Http request class
 */
class Request {

    /**
     * @var array
     */
    private $httpGetData = [];

    /**
     * @var array
     */
    private $httpPostData = [];

    /**
     * Request constructor.
     *
     * @param array $httpGetData
     * @param array $httpPostData
     */
    public function __construct(array $httpGetData, array $httpPostData) {
        $this->httpGetData = $httpGetData;
        $this->httpPostData = $httpPostData;
    }

    /**
     * @return static
     */
    public static function createFromGlobals() {
        return new static($_GET, $_POST);
    }

    /**
     * @param string $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = NULL) {

        if (array_key_exists($key, $this->httpGetData)) {
            return $this->httpGetData[$key];
        }

        if (array_key_exists($key, $this->httpPostData)) {
            return $this->httpPostData[$key];
        }

        return $default;
    }
}