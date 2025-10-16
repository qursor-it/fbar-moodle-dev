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

namespace auth_spid\util;

use moodle_url;

/**
 * Utility class for accessing plugin configuration.
 *
 * @package     auth_spid
 * @copyright   2025 Finzioni
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config {
    /** @var array|null */
    protected ?array $cache = null;

    /**
     * Load configuration lazily.
     */
    protected function load(): void {
        if ($this->cache !== null) {
            return;
        }
        $defaults = [
            'entityid' => '',
            'acs' => '',
            'slo' => '',
            'metadatacache' => '',
            'certpath' => '',
            'certduration' => 365,
            'attrmap' => '{}',
            'autoupdate' => 0,
            'buttonlabel' => get_string('button:login', 'auth_spid'),
            'debug' => 0,
        ];
        $values = [];
        foreach ($defaults as $key => $default) {
            $values[$key] = get_config('auth_spid', $key) ?? $default;
        }
        $values['metadataurl'] = (new moodle_url('/auth/spid/metadata.php'))->out(false);
        $this->cache = $values;
    }

    /**
     * Get single value.
     */
    public function get(string $key, $default = null) {
        $this->load();
        return $this->cache[$key] ?? $default;
    }

    /**
     * Get attribute mapping as array.
     */
    public function get_attribute_mapping(): array {
        $this->load();
        $raw = $this->cache['attrmap'] ?? '{}';
        if (empty($raw)) {
            return [];
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }
        return $decoded;
    }

    /**
     * Return the absolute path for certificate storage.
     */
    public function get_certificate_directory(): string {
        global $CFG;
        $this->load();
        $configured = $this->cache['certpath'] ?? '';
        if (!empty($configured)) {
            return $configured;
        }
        return $CFG->dataroot . '/spidcerts';
    }

    /**
     * Return metadata cache file path.
     */
    public function get_metadata_cache_path(): string {
        $this->load();
        if (!empty($this->cache['metadatacache'])) {
            return $this->cache['metadatacache'];
        }
        return $this->get_certificate_directory() . '/metadata.xml';
    }
}
