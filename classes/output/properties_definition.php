<?php

namespace tool_lazydev\output;

class properties_definition implements \renderable {

    public $definition;

    /**
     * properties_definition constructor.
     *
     * @param $definition
     */
    public function __construct($definition) {
        $this->definition = $definition;
    }

}