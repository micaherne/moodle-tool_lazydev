<?php

namespace tool_lazydev\local\reader;

use tool_lazydev\local\model;

interface model_reader {

    public function get_model(): model;

}