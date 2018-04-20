<?php

namespace tool_lazydev\output;

class renderer extends \renderer_base {

    protected function render_properties_definition(properties_definition $definition) {
        $result = var_export($definition->definition, true);

        // Convert the strings with constant names back to constants.
        $result = preg_replace('/\'(PARAM_[A-Z]+)\'/', '$1', $result);
        $result = 'return ' . $result . ';';
        return $result;
    }

    protected function render_database_table_metadata(database_table_metadata $metadata) {
        return $this->render_from_template('tool_lazydev/database_table_metadata', $metadata->export_for_template($this));
    }

    protected function render_database_table_metadata_langstrings(database_table_metadata_langstrings $metadata) {
        return $this->render_from_template('tool_lazydev/database_table_metadata_langstrings',
            $metadata->export_for_template($this));
    }


}