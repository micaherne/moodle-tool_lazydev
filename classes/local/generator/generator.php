<?php

namespace tool_lazydev\local\generator;

use tool_lazydev\local\model;
use tool_lazydev\local\reader\model_reader;

abstract class generator {

    /**
     * @var model_reader
     */
    protected $model_reader;

    public function __construct(model_reader $modelreader) {
        $this->model_reader = $modelreader;
    }

}