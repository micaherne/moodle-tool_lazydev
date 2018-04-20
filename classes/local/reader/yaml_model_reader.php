<?php

namespace tool_lazydev\local\reader;

use tool_lazydev\local\model;

class yaml_model_reader implements model_reader {

    /** @var model */
    protected $model;

    public function __construct(string $yaml) {
        global $CFG;
        $this->yaml = $yaml;
        require_once($CFG->dirroot . '/admin/tool/lazydev/lib/spyc/Spyc.php');
        $this->model = new model(spyc_load($yaml));
    }

    public function get_model() : model {
        return $this->model;
    }

}