<?php

use tool_lazydev\local\reader\yaml_model_reader;

class tool_lazydev_yaml_model_reader_testcase extends \advanced_testcase {

    public function test_constructor() {
        $y = new yaml_model_reader(file_get_contents(__DIR__ . '/yaml/test1.yaml'));
        $this->assertNotNull($y->get_model()->get_entity_metadata('block_strathsurveys'));
        return $y;
    }

}