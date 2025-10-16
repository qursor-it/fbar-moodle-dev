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
use Exception;

/**
 * Generates self-signed certificates for SPID metadata.
 *
 * @package     auth_spid
 */
class generator {
    public function __construct(protected repository $repository, protected config $config) {
    }

    /**
     * Generate a new certificate/key pair.
     */
    public function generate(): bool {
        $entityid = $this->config->get('entityid');
        if (empty($entityid)) {
            core\notification::add(get_string('error:missingdependency', 'auth_spid'), core\notification::WARNING);
            return false;
        }

        $dn = [
            'commonName' => $entityid,
            'organizationName' => $entityid,
            'countryName' => 'IT',
        ];
        $privkey = openssl_pkey_new([
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        if ($privkey === false) {
            core\notification::add('Unable to generate private key for SPID', core\notification::ERROR);
            return false;
        }

        $csr = openssl_csr_new($dn, $privkey, ['digest_alg' => 'sha256']);
        $duration = (int)$this->config->get('certduration', 365);
        $cert = openssl_csr_sign($csr, null, $privkey, $duration, ['digest_alg' => 'sha256']);

        openssl_pkey_export($privkey, $privatekey);
        openssl_x509_export($cert, $publiccert);

        $this->repository->save($publiccert, $privatekey);
        return true;
    }

    /**
     * Retrieve the current pair, optionally generating if missing.
     */
    public function get_or_create(bool $autocreate = true): array {
        [$cert, $key] = $this->repository->load();
        if ($cert && $key) {
            return [$cert, $key];
        }
        if ($autocreate) {
            if ($this->generate()) {
                return $this->repository->load();
            }
        }
        return [$cert, $key];
    }
}
