<?php

use tool_lazydev\local\generator\persistent_generator;
use tool_lazydev\local\reader\yaml_model_reader;

class tool_lazydev_persistent_generator_testcase extends \advanced_testcase {

    public function test_constructor() {
        $y = new yaml_model_reader(file_get_contents(__DIR__ . '/yaml/test1.yaml'));
        $gen = new persistent_generator($y);

        return $gen;
    }

    /**
     * @depends test_constructor
     */
    public function test_get_properties_definition(persistent_generator $gen) {
        $props1 = $gen->get_properties_definition('block_strathsurveys');
        $this->assertCount(2, $props1);
        $this->assertArrayHasKey('name', $props1);
        $this->assertArrayHasKey('datestart', $props1);
        $this->assertArrayHasKey('choices', $props1['name']);
    }

    /**
     * @depends test_constructor
     */
    public function test_get_properties_definition_code(persistent_generator $gen) {
        $code1 = $gen->get_properties_definition_code('block_strathsurveys');
        $props1 = eval("return $code1;");
        $this->assertEquals(PARAM_TEXT, $props1['name']['type']);
    }

    /**
     * @depends test_constructor
     */
    public function test_get_base_class_code(persistent_generator $gen) {
        $code1 = $gen->get_base_class_code('block_strathsurveys', 'tool_box');
        $ast = eval(substr($code1, 5));

        $a = new \tool_box\local\model\generated\block_strathsurveys_base();
        $this->assertNotNull($a);

        $reflect1 = new ReflectionClass($a);

        $this->assertEquals('block_strathsurveys', $reflect1->getConstant('TABLE'));

        return $gen;
    }

    /**
     * @depends test_get_base_class_code
     */
    public function test_get_main_class_code(persistent_generator $gen) {
        $code1 = $gen->get_base_main_code('block_strathsurveys', 'tool_box');
        $ast = eval(substr($code1, 5));

        $a = new \tool_box\local\model\block_strathsurveys();
        $this->assertNotNull($a);

        $reflect1 = new ReflectionClass($a);

        $this->assertEquals('block_strathsurveys', $reflect1->getConstant('TABLE'));
    }

}