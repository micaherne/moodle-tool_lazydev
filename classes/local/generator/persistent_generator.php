<?php

namespace tool_lazydev\local\generator;

use core\persistent;

class persistent_generator extends generator {

    protected const PROPERTIES_OF_INTEREST = ['moodle_type', 'default', 'nullable', 'message', 'choices'];

    /**
     * Get a representation of the properties definition.
     *
     * Note that PARAM_* constants will be represented by strings, as these are required
     * for code generation and can't be inferred from their value.
     *
     * @param string $entityname
     * @return array
     * @throws \coding_exception
     */
    public function get_properties_definition(string $entityname) {

        $entitydef = $this->model_reader->get_model()->get_entity_metadata($entityname);

        $defaultfields = persistent::properties_definition();

        $result = [];
        foreach ($entitydef['fields'] as $fieldname => $fielddef) {
            if (!is_array($fielddef)) {
                // debugging("Field $fieldname should have an array of properties.");
                continue;
            }

            if (empty($fielddef['moodle_type'])) {
                debugging("moodle_type not set for $fieldname");
                continue;
            }

            $fields = array_filter($fielddef, function($key) {
                return in_array($key, self::PROPERTIES_OF_INTEREST);
            }, ARRAY_FILTER_USE_KEY);

            if (array_key_exists('nullable', $fields)) {
                $fields['null'] = !$fields['nullable'] ? NULL_NOT_ALLOWED : NULL_ALLOWED;
                unset($fields['nullable']);
            }

            $fields['type'] = $fields['moodle_type'];
            unset($fields['moodle_type']);

            // Standard properties should only use the type attribute.
            // See https://docs.moodle.org/dev/Exporter#Property_attributes.
            $result[$fieldname] = $fields;
        }


        return $result;

    }

    public function get_properties_definition_code(string $entityname) {
        $result = var_export($this->get_properties_definition($entityname), true);
        $result = preg_replace("@'(PARAM_[A-Z]+)'@", '$1', $result);
        return $result;
    }

    public function get_base_class_code(string $entityname, string $component) {
        $defaultnamespace = $component . '\\local\\model\\generated';
        $defaultclassname = $entityname . '_base';
        $defaulttablename = $entityname;

        $propertiesdefinitioncode = $this->get_properties_definition_code($entityname);

        $result = "<?php
        namespace $defaultnamespace;
        
        use core\persistent;
        
        class $defaultclassname extends persistent {
        
            const TABLE = '$defaulttablename';
            
            protected static function define_properties() {
                return $propertiesdefinitioncode;
            }
            
        }";

        return $result;
    }

    public function get_base_main_code(string $entityname, string $component) {
        $defaultnamespace = $component . '\\local\\model';
        $defaultclassname = $entityname;
        $defaulttablename = $entityname;

        $propertiesdefinitioncode = $this->get_properties_definition_code($entityname);

        $result = "<?php
        namespace $defaultnamespace;
        
        use $component\\local\\model\\generated\\{$defaultclassname}_base;
        
        class $defaultclassname extends {$defaultclassname}_base {

        }";

        return $result;
    }


}