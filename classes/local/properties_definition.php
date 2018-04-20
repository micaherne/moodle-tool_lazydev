<?php

namespace tool_lazydev\local;

class properties_definition {

    /** @var array */
    protected $entitymetadata;

    protected $name;

    /**
     * properties_definition constructor.
     *
     * @param array $entitymetadata
     */
    public function __construct(string $name, array $entitymetadata) {
        $this->name = $name;
        $this->entitymetadata = $entitymetadata;
    }

    /**
     * Gets an array of data representing the entity's properties definition.
     *
     * Note that this is not a valid properties definition in itself as the
     * type fields contain the string name of the Moodle type, not the value.
     *
     * This is necessary for code generation, as the constant name can't be
     * inferred from the value.
     *
     * @return array
     */
    public function get_data() {
        $result = [];
        foreach ($this->entitymetadata['fields'] as $fieldname => $fielddef) {
            $def = [];
            if (empty($fielddef[self::MOODLE_TYPE])) {
                if ($fieldname == 'id') {
                    $def[self::TYPE] = 'PARAM_INT';
                } else {
                    debugging('Moodle type required for ' . $this->name . '.' . $fieldname);
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

            $result[$fieldname] = $def;
        }

        return $result;
    }


}