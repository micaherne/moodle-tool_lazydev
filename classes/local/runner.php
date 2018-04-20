<?php

namespace tool_lazydev\local;

use tool_lazydev\local\generator\persistent_generator;
use tool_lazydev\local\generator\xmldb_generator;
use tool_lazydev\local\reader\yaml_model_reader;

class runner {

    protected $plugin;
    protected $plugindir;

    public function __construct($plugin) {
        $this->plugindir = \core_component::get_component_directory($this->plugin);
        if (empty($this->plugindir)) {
            throw new \coding_error("Unknown plugin {$plugin} - use frankenstyle name");
        }
        $this->plugin = $this->plugin;
    }

    public function generate_all() {
        $yamlfile = $this->plugindir . '/db/tool_lazydev.yaml';
        if (!file_exists($yamlfile)) {
            mtrace("tool_lazydev.yaml not found");
            exit(1);
        }
        
        $y = new yaml_model_reader(file_get_contents($yamlfile));
        
        $persistententities = $y->get_model()->get_persistent_entities();
        $gen = new persistent_generator($y);
        
        foreach (array_keys($persistententities) as $entity) {
            mtrace("Generating persistent $entity");
            $basecode = $gen->get_base_class_code($entity, $this->plugin);
            $maincode = $gen->get_main_class_code($entity, $this->plugin);
            file_put_contents($this->plugindir . "/classes/local/model/generated/{$entity}_base.php", $basecode);
            
            $mainfile = $this->plugindir . "/classes/local/model/{$entity}.php";
            if (!file_exists($mainfile)) {
                file_put_contents($mainfile, $maincode);
            }
            
        }
        
        $xgen = new xmldb_generator($y);
        $xmldb = $xgen->generate_xmldb_structure($this->plugin);
        $xml = $xmldb->xmlOutput();
        file_put_contents($this->plugindir . '/db/install.xml', $xml);
    }

}