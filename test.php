<?php

define('CLI_SCRIPT', 1);
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

use tool_lazydev\local\runner;

list($params, $other) = cli_get_params([
    'plugin' => null
], [
    'p' => 'plugin'
]);

$plugin = $params['plugin'];

$runner = new runner($plugin);
$runner->generate_all();
