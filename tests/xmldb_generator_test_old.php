<?php

use core\persistent;
use tool_lazydev\local\xmldb_generator;

class tool_lazydev_xmldb_generator_testcase extends \advanced_testcase {

    public function test_tabledef_for_persistent() {
        $persistent1 = new class extends persistent {

            const TABLE = 'interesting_facts';

            // From docs.
            protected static function define_properties() {
                return array(
                    'userid' => array(
                        'type' => PARAM_INT,
                    ),
                    'message' => array(
                        'type' => PARAM_RAW,
                    )
                );
            }
        };

        $gen = new xmldb_generator();
        $def = $gen->tabledef_for_persistent($persistent1);
        $fields = $def->getFields();
        $this->assertEquals('id', $fields[0]->getName(), 'id field should be first');
        $this->assertNotEmpty($def->getField('timecreated'));
        $this->assertEquals(XMLDB_TYPE_INTEGER, $def->getField('userid')->getType());

    }

    public function test_infer_column_details() {
        $gen = new xmldb_generator();
        $paramint = $gen->infer_column_details(PARAM_INT);
        $this->assertEquals(['type' => XMLDB_TYPE_INTEGER, 'precision' => 10], $paramint);
    }

}