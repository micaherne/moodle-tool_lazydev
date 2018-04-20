<?php

use tool_lazydev\local\generator\privacy_generator;
use tool_lazydev\local\reader\yaml_model_reader;

class tool_lazydev_privacy_generator_testcase extends \advanced_testcase {

    public function test_constructor() {
        $y = new yaml_model_reader(file_get_contents(__DIR__ . '/yaml/test1.yaml'));
        $gen = new privacy_generator($y);

        return $gen;
    }

    /**
     * @depends test_constructor
     */
    public function test_generate_database_table_metadata(privacy_generator $gen) {
        echo $gen->generate_database_table_metadata('essay');
    }

}