<?php

namespace Http\Rendering;

use Http\ValueObject\Response;

class JsonRendering {

    /**
     * @param array $data
     *
     * @return Response
     */
    public function render(array $data = []) {
        return Response::create(json_encode($data));
    }
}