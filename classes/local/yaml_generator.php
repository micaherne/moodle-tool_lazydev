<?php

namespace tool_lazydev\local;

use tool_lazydev\output\database_table_metadata;
use tool_lazydev\output\database_table_metadata_langstrings;

class yaml_generator {

    protected $yaml;
    protected $config;

    const MOODLE_TYPE = 'moodle_type';
    const ENTITIES = 'entities';
    const TYPE = 'type';

    const PRECISION = 'precision';

    public function __construct(string $yaml) {
        global $CFG;
        $this->yaml = $yaml;
        require_once($CFG->dirroot . '/admin/tool/lazydev/lib/Spyc.php');
        $this->config = spyc_load($yaml);
    }

    /**
     * Generate an array of properties_definitions.
     *
     * This should be the actual data structure returned by properties_definition() in a persistent or exporter
     * but actually returns the type as a string containing the name of the constant, as we're currently using
     * var_export to generate the code, which can't reverse engineer the PARAM_* constants.
     *
     * @todo Make this return the actual code, and get rid of the renderer / renderables.
     * @return array
     */
    public function get_properties_definitions() {
        $result = [];

        foreach ($this->config[self::ENTITIES] as $entityname => $entitydef) {
            $e = [];
            foreach ($entitydef['fields'] as $fieldname => $fielddef) {
                $def = [];
                if (empty($fielddef[self::MOODLE_TYPE])) {
                    if ($fieldname == 'id') {
                        $def[self::TYPE] = 'PARAM_INT';
                    } else {
                        debugging('Moodle type required for ' . $entityname . '.' . $fieldname);
                        continue;
                    }
                } else {
                    $def[self::TYPE] = $fielddef[self::MOODLE_TYPE];
                }
                if (!empty($fielddef['default'])) {
                    $def['default'] = $fielddef['default'];
                }
                if (!empty($fielddef['nullable'])) {
                    $def['null'] = $fielddef['nullable'] ? NULL_ALLOWED : NULL_NOT_ALLOWED;
                }
                if (!empty($fielddef['message'])) {
                    // TODO: Must be a lang_string instance.
                }
                if (!empty($fielddef['choices'])) {
                    $choices = $fielddef['choices'];
                    if (!empty($choices)) {
                        if (is_array($choices)) {
                            $def['choices'] = $choices;
                        } else {
                            // TODO: Validate the choices against the Moodle type.
                            debugging("Choices must be an array");
                        }
                    }
                }

                $e[$fieldname] = $def;
            }
            $result[$entityname] = $e;
        }

        return $result;
    }

    public function get_database_table_metadata($entity) : string {
        global $PAGE;

        if (empty($this->config[self::ENTITIES][$entity])) {
            throw new \coding_exception("Entity $entity does not exist");
        }

        $entitydef = $this->config[self::ENTITIES][$entity];
        $output = $PAGE->get_renderer('tool_lazydev');
        $md = new database_table_metadata($entity, $entitydef['fields']);

        return $output->render($md);
    }

    public function get_database_table_metadata_langstrings($entity) : string {
        global $PAGE;

        if (empty($this->config[self::ENTITIES][$entity])) {
            throw new \coding_exception("Entity $entity does not exist");
        }

        $entitydef = $this->config[self::ENTITIES][$entity];
        $output = $PAGE->get_renderer('tool_lazydev');
        $md = new database_table_metadata_langstrings($entity, $entitydef['fields']);

        return $output->render($md);
    }

    public function get_xmldb_structure() : \xmldb_structure {
        $result = new \xmldb_structure('tool_lazydev');

        foreach ($this->config[self::ENTITIES] as $entityname => $entitydef) {
            if (!isset($entitydef['persistent']) || $entitydef['persistent'] == 'false') {
                continue;
            }
            $table = new \xmldb_table($entityname);
            foreach ($entitydef['fields'] as $propname => $propdef) {
                $params = [
                    'name' => $propname,
                    self::TYPE => XMLDB_TYPE_CHAR,
                    self::PRECISION => null,
                    'unsigned' => null,
                    'notnull' => false,
                    'sequence' => $propname == 'id',
                    'default' => null,
                    'comment' => ''
                ];

                if (empty($propdef)) {
                    $propdef = [];
                }
                foreach ($propdef as $feature => $value) {
                    if ($feature == self::MOODLE_TYPE) {
                        $value = clean_param($value, PARAM_ALPHANUMEXT);
                        if (defined($value)) {
                            $value = constant($value);
                        }
                        $details = $this->infer_column_details($value);
                        $params = array_merge($params, $details); // Later keys overwrite.
                    } else if ($feature == 'db_type') {
                        $field = new \xmldb_field($propname);
                        $typeconst = $field->getXMLDBFieldType($value);
                        if ($typeconst !== XMLDB_TYPE_INCORRECT) {
                            $params[self::TYPE] = $typeconst;
                        }
                    } else if ($feature == 'nullable') {
                        $params['notnull'] = !$value ? 'TRUE' : 'FALSE';
                    } else if ($feature == 'default') {
                        $params['default'] = $value;
                    } else if ($feature == 'length') {
                        $params[self::PRECISION] = $value;
                    } else if ($feature == 'message' || $feature == 'choices') {
                        // These are allowed, but we don't need to do anything with them.
                    } else if ($feature == 'privacy') {
                        // Not required for XMLDB.
                    } else {
                        if (array_key_exists($feature, $params)) {
                            $params[$feature] = $value;
                        } else {
                            debugging('Unknown property attribute ' . $feature);
                            continue;
                        }
                    }
                }

                $table->add_field($params['name'], $params[self::TYPE], $params[self::PRECISION], $params['unsigned'],
                    $params['notnull'], $params['sequence'], $params['default']);

            }

            if (!empty($table->getFields())) {
                $result->addTable($table);
            }

        }
        return $result;
    }

    public function infer_column_details(string $paramconstant) : array {
        $result = [];
        switch ($paramconstant) {
            case PARAM_INT:
            case PARAM_INTEGER:
                $result[self::TYPE] = XMLDB_TYPE_INTEGER;
                $result[self::PRECISION] = 10;
                break;
            case PARAM_FLOAT:
            case PARAM_NUMBER:
                $result[self::TYPE] = XMLDB_TYPE_NUMBER;
                $result[self::PRECISION] = 10;
            default:
                $result[self::TYPE] = XMLDB_TYPE_CHAR;
                $result[self::PRECISION] = 255;
        }
        return $result;
    }

}