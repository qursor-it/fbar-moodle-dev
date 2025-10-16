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

namespace auth_spid\certificates;

use auth_spid\util\config;
use core\notification;
use coding_exception;

/**
 * Handles certificate storage and retrieval.
 *
 * @package     auth_spid
 */
class repository {
    public const CERT_FILENAME = 'spid.crt';
    public const KEY_FILENAME = 'spid.key';

    public function __construct(protected config $config) {
    }

    /**
     * Ensure certificate directory exists and is protected.
     */
    protected function ensure_directory(): string {
        $path = $this->config->get_certificate_directory();
        if (!file_exists($path)) {
            if (!mkdir($path, $mode = 0700, true)) {
                throw new coding_exception('Unable to create SPID certificate directory: ' . $path);
            }
        }
        return $path;
    }

    /**
     * Return certificate path.
     */
    public function get_certificate_path(): string {
        return $this->ensure_directory() . '/' . self::CERT_FILENAME;
    }

    /**
     * Return key path.
     */
    public function get_key_path(): string {
        return $this->ensure_directory() . '/' . self::KEY_FILENAME;
    }

    /**
     * Save certificate and key contents.
     */
    public function save(string $certificate, string $privatekey): void {
        $certpath = $this->get_certificate_path();
        $keypath = $this->get_key_path();
        file_put_contents($certpath, $certificate);
        chmod($certpath, 0600);
        file_put_contents($keypath, $privatekey);
        chmod($keypath, 0600);
        set_config('certificate', $certificate, 'auth_spid');
        set_config('privatekey', $privatekey, 'auth_spid');
        core\notification::add(get_string('metadata:generated', 'auth_spid'), core\notification::SUCCESS);
    }

    /**
     * Load certificate/key pair if available.
     */
    public function load(): array {
        $certpath = $this->get_certificate_path();
        $keypath = $this->get_key_path();
        if (!file_exists($certpath) || !file_exists($keypath)) {
            $configcert = get_config('auth_spid', 'certificate') ?? '';
            $configkey = get_config('auth_spid', 'privatekey') ?? '';
            return [$configcert, $configkey];
        }
        return [file_get_contents($certpath), file_get_contents($keypath)];
    }
}
