<?php

namespace tool_lazydev\output;

class database_table_metadata implements \renderable, \templatable {

    public $table;
    public $columns;

    public function __construct($table, $columns) {
        $this->table = $table;
        $this->columns = $columns;
    }

    public function export_for_template(\renderer_base $output) {
        $result = ['table' => $this->table];
        $columnslist = [];
        foreach ($this->columns as $columnname => $columndef) {
            $columnslist[] = ['name' => $columnname];
        }

        $columnslist[count($columnslist) - 1]['last'] = true;
        $result['columnslist'] = $columnslist;
        return $result;
    }

}