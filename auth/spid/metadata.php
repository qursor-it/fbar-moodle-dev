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

require(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/auth/spid/lib.php');

auth_spid_load_vendor();

$config = new \auth_spid\util\config();
$repository = new \auth_spid\certificates\repository($config);
$generator = new \auth_spid\certificates\generator($repository, $config);
$builder = new \auth_spid\metadata\builder($config, $generator);
$service = new \auth_spid\api\spid_service($config, $builder, $generator);

try {
    $metadata = $service->get_metadata();
} catch (\Throwable $e) {
    throw new moodle_exception('cli:regenerateerror', 'auth_spid', '', $e->getMessage());
}

header('Content-Type: application/samlmetadata+xml; charset=utf-8');
echo $metadata;
exit;
