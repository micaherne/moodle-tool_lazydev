<?php

use tool_lazydev\output\properties_definition;
use tool_lazydev\output\renderer;

class tool_lazydev_renderer_testcase extends \advanced_testcase {

    public function test_render_properties_definition() {
        global $PAGE;

        $defs = array (
            'essay' =>
                array (
                    'id' =>
                        array (
                            'type' => 'PARAM_INT',
                        ),
                    'name' =>
                        array (
                            'type' => 'PARAM_TEXT',
                            'choices' =>
                                array (
                                    0 => 'one',
                                    1 => 'two',
                                    2 => 'three',
                                ),
                        ),
                    'datestart' =>
                        array (
                            'type' => 'PARAM_INT',
                        ),
                ),
        );

        $renderable = new properties_definition($defs['essay']);

        $renderer = $PAGE->get_renderer('tool_lazydev');

        $code = $renderer->render($renderable);
        $var = eval($code);

        $this->assertArrayHasKey('id', $var);
        $this->assertEquals(PARAM_TEXT, $var['name']['type']);
    }

}