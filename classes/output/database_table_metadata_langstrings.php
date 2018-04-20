<?php

namespace tool_lazydev\output;

class database_table_metadata_langstrings extends database_table_metadata {

    public function export_for_template(\renderer_base $output) {
        $result = parent::export_for_template($output);

        foreach ($result['columnslist'] as &$column) {
            if (!empty($this->columns[$column['name']]['privacy'])) {
                $column['string'] = $this->columns[$column['name']]['privacy'];
            }
        }

        return $result;
    }

}