<?php

namespace Queue\Exception;

class ErrorHandlerNotFoundException extends \LogicException {

    /**
     * @param \Exception $exception
     *
     * @return ErrorHandlerNotFoundException
     */
    public static function createFromException(\Exception $exception) {
        return new self($exception->getCode(), $exception->getCode(), $exception);
    }
}