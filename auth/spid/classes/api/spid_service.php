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

namespace auth_spid\api;

use auth_spid\certificates\generator as cert_generator;
use auth_spid\metadata\builder;
use auth_spid\util\config;
use coding_exception;
use core\notification;
use moodle_exception;

/**
 * Facade for SPID operations.
 *
 * @package     auth_spid
 */
class spid_service {
    public function __construct(
        protected config $config,
        protected builder $metadatabuilder,
        protected cert_generator $certgenerator,
    ) {
    }

    /**
     * Ensure auth_saml2 plugin is available.
     */
    public function assert_dependency(): void {
        if (!class_exists('auth_plugin_saml2')) {
            throw new moodle_exception('error:missingdependency', 'auth_spid');
        }
    }

    /**
     * Ensure PHP library is available.
     */
    public function assert_vendor(): void {
        if (!class_exists('SPID_PHP\SPID_CIE\Authn\AuthnRequest')) {
            throw new moodle_exception('error:missingvendor', 'auth_spid');
        }
    }

    /**
     * Build metadata and optionally persist to cache file.
     */
    public function generate_metadata(bool $persist = true): string {
        $this->assert_dependency();
        $xml = $this->metadatabuilder->build();
        if ($persist) {
            $path = $this->config->get_metadata_cache_path();
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0700, true);
            }
            file_put_contents($path, $xml);
        }
        return $xml;
    }

    /**
     * Return metadata from cache or regenerate.
     */
    public function get_metadata(): string {
        $path = $this->config->get_metadata_cache_path();
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return $this->generate_metadata(true);
    }

    /**
     * Retrieve current certificate.
     */
    public function get_certificate_pair(): array {
        return $this->certgenerator->get_or_create(false);
    }
}
