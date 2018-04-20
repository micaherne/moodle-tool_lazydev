<?php

use tool_lazydev\local\generator\xmldb_generator;
use tool_lazydev\local\reader\yaml_model_reader;

class tool_lazydev_xmldb_generator_testcase extends \advanced_testcase {

    public function test_constructor() {
        $y = new yaml_model_reader(file_get_contents(__DIR__ . '/yaml/test1.yaml'));
        $g = new xmldb_generator($y);

        return $g;
    }

    /**
     * @depends test_constructor
     * @param xmldb_generator $g
     */
    public function test_generate_xmldb_table(xmldb_generator $g) {
        $table1 = $g->generate_xmldb_table('essay');
        echo $table1->xmlOutput();
    }

    /**
     * @depends test_constructor
     * @param xmldb_generator $g
     */
    public function test_generate_xmldb_structure(xmldb_generator $g) {
        $structure = $g->generate_xmldb_structure('local_hero');
        $xml = $structure->xmlOutput();
        // Is path still required?
        // $this->assertContains('PATH="local/hero/db"', $xml);
        $this->assertCount(1, $structure->getTables());
    }

    /**
     * @depends test_constructor
     */
    public function test_infer_column_details_for_moodle_type($g) {
        $paramint = $g->infer_column_details_for_moodle_type(PARAM_INT);
        $this->assertEquals(['type' => XMLDB_TYPE_INTEGER, 'precision' => 10], $paramint);
    }

}