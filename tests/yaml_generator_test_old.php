<?php

use tool_lazydev\local\yaml_generator;

class tool_lazydev_yaml_generator_testcase extends \advanced_testcase {

    public function test_construct() {
        $y = new yaml_generator(file_get_contents(__DIR__ . '/yaml/test1.yaml'));
        return $y;
    }

    /**
     * @depends test_construct
     */
    public function test_get_xmldb_structure(yaml_generator $y) {
        $xmldb = $y->get_xmldb_structure();
    }

    /**
     * @depends test_construct
     */
    public function test_get_properties_definitions(yaml_generator $y) {
        $props = $y->get_properties_definitions();
        $this->assertArrayHasKey('essay', $props);
    }

    /**
     * @depends test_construct
     */
    public function test_get_database_table_metadata(yaml_generator $y) {
        $code = $y->get_database_table_metadata('essay');
        print_r($code);
    }

    /**
     * @depends test_construct
     */
    public function test_get_database_table_metadata_langstrings(yaml_generator $y) {
        $code = $y->get_database_table_metadata_langstrings('essay');
        print_r($code);
    }
}