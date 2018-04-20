<?php

namespace tool_lazydev\local;

class model {

    protected $metadata;

    /**
     * model constructor.
     *
     * @param array $metadata
     */
    public function __construct(array $metadata) {
        $this->metadata = $metadata;
    }

    /**
     * @return array
     */
    public function get_metadata(): array {
        return $this->metadata;
    }

    public function get_entity_metadata(string $entityname) : array {
        if (isset($this->metadata['entities']) && array_key_exists($entityname, $this->metadata['entities'])) {
            return $this->metadata['entities'][$entityname];
        }
        throw new \coding_exception("Entity $entityname does not exist in model.");
    }

    public function get_persistent_entities() {
        return array_filter($this->metadata['entities'], function($entity) {
            return !empty($entity['persistent']);
        });
    }

}