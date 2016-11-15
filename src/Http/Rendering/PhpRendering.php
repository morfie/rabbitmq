<?php

namespace Http\Rendering;

use Http\ValueObject\Response;

class PhpRendering {

    /**
     * @param $template
     *
     * @return Response
     */
    public function render($template) {
        ob_start();
        require $template;
        return Response::create(ob_get_clean());
    }
}