<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/auth/spid/lib.php');

auth_spid_load_vendor();

list($options, $unrecognized) = cli_get_params([
    'help' => false,
], [
    'h' => 'help',
]);

if (!empty($options['help'])) {
    $help = "CLI tool for regenerating SPID metadata.\n\nOptions:\n-h, --help          Print this help.\n";
    cli_writeln($help);
    exit(0);
}

$config = new \auth_spid\util\config();
$repository = new \auth_spid\certificates\repository($config);
$generator = new \auth_spid\certificates\generator($repository, $config);
$builder = new \auth_spid\metadata\builder($config, $generator);
$service = new \auth_spid\api\spid_service($config, $builder, $generator);

try {
    $xml = $service->generate_metadata(true);
    cli_writeln(get_string('cli:regenerated', 'auth_spid', $config->get_metadata_cache_path()));
} catch (\Throwable $e) {
    cli_error(get_string('cli:regenerateerror', 'auth_spid', $e->getMessage()));
}
