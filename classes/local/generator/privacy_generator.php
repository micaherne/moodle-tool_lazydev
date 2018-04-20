<?php

namespace tool_lazydev\local\generator;

class privacy_generator extends generator {

    public function generate_database_table_metadata($entityname) {
        global $PAGE;

        $template = "\$collection->add_database_table('{{table}}', [
        {{#columnslist}}    '{{name}}' => 'privacy:metadata:{{table}}:{{name}}'{{^last}},
        {{/last}}{{/columnslist}}
        ], 'privacy:metadata:{{table}}');";

        $context = ['table' => $entityname, 'columnslist' => []];
        foreach ($this->model_reader->get_model()->get_entity_metadata($entityname)['fields'] as $fieldname => $fielddef) {
            if (!is_array($fielddef)) {
                // TODO: Debugging.
                continue;
            }
            if (array_key_exists('privacy', $fielddef)) {
                $context['columnslist'][] = ['name' => $fieldname];
            }
        }
        if (!empty($context['columnslist'])) {
            $context['columnslist'][count($context['columnslist']) - 1]['last'] = true;
        }

    }

}