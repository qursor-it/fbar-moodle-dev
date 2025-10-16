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

namespace auth_spid\task;

use auth_spid\api\spid_service;
use auth_spid\certificates\generator;
use auth_spid\certificates\repository;
use auth_spid\metadata\builder;
use auth_spid\util\config;

/**
 * Scheduled task for metadata regeneration.
 */
class metadata_refresh_task extends \core\task\scheduled_task {
    public function get_name(): string {
        return get_string('task:metadatarefresh', 'auth_spid');
    }

    public function execute(): void {
        global $CFG;
        require_once($CFG->dirroot . '/auth/spid/lib.php');
        auth_spid_load_vendor();

        $config = new config();
        $repository = new repository($config);
        $generator = new generator($repository, $config);
        $builder = new builder($config, $generator);
        $service = new spid_service($config, $builder, $generator);
        try {
            $xml = $service->generate_metadata(true);
            mtrace(get_string('metadata:regenerated', 'auth_spid'));
        } catch (Throwable $e) {
            mtrace('SPID metadata regeneration failed: ' . $e->getMessage());
        }
    }
}
