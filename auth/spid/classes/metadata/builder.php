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

namespace auth_spid\metadata;

use auth_spid\certificates\generator;
use auth_spid\util\config;
use DOMDocument;
use DOMElement;
use coding_exception;

/**
 * Builds SPID metadata XML documents.
 *
 * @package     auth_spid
 */
class builder {
    public function __construct(protected config $config, protected generator $certgenerator) {
    }

    /**
     * Generate metadata XML string.
     */
    public function build(): string {
        $entityid = $this->config->get('entityid');
        $acs = $this->config->get('acs');
        $slo = $this->config->get('slo');
        if (!$entityid || !$acs) {
            throw new coding_exception('Missing SPID entityID or ACS URL configuration');
        }

        [$certificate] = $this->certgenerator->get_or_create();
        if (empty($certificate)) {
            throw new coding_exception(get_string('metadata:signaturemissing', 'auth_spid'));
        }
        $certificate = trim(preg_replace('/-----BEGIN CERTIFICATE-----|-----END CERTIFICATE-----/', '', $certificate));
        $certificate = preg_replace('/\s+/', '', $certificate);

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $entitydescriptor = $doc->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata', 'md:EntityDescriptor');
        $entitydescriptor->setAttribute('entityID', $entityid);
        $doc->appendChild($entitydescriptor);

        $spdescriptor = $doc->createElement('md:SPSSODescriptor');
        $spdescriptor->setAttribute('protocolSupportEnumeration', 'urn:oasis:names:tc:SAML:2.0:protocol');
        $spdescriptor->setAttribute('AuthnRequestsSigned', 'true');
        $spdescriptor->setAttribute('WantAssertionsSigned', 'true');
        $entitydescriptor->appendChild($spdescriptor);

        $keydescriptor = $doc->createElement('md:KeyDescriptor');
        $keydescriptor->setAttribute('use', 'signing');
        $spdescriptor->appendChild($keydescriptor);

        $keyinfo = $doc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:KeyInfo');
        $keydescriptor->appendChild($keyinfo);

        $x509data = $doc->createElement('ds:X509Data');
        $keyinfo->appendChild($x509data);

        $x509certificate = $doc->createElement('ds:X509Certificate', $certificate);
        $x509data->appendChild($x509certificate);

        $acsnode = $doc->createElement('md:AssertionConsumerService');
        $acsnode->setAttribute('Binding', 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST');
        $acsnode->setAttribute('Location', $acs);
        $acsnode->setAttribute('index', '0');
        $acsnode->setAttribute('isDefault', 'true');
        $spdescriptor->appendChild($acsnode);

        if ($slo) {
            $slonode = $doc->createElement('md:SingleLogoutService');
            $slonode->setAttribute('Binding', 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect');
            $slonode->setAttribute('Location', $slo);
            $spdescriptor->appendChild($slonode);
        }

        $attrconsuming = $doc->createElement('md:AttributeConsumingService');
        $attrconsuming->setAttribute('index', '1');
        $spdescriptor->appendChild($attrconsuming);

        $serviceinfo = $doc->createElement('md:ServiceName', 'Moodle SPID');
        $serviceinfo->setAttribute('xml:lang', 'it');
        $attrconsuming->appendChild($serviceinfo);

        foreach ($this->config->get_attribute_mapping() as $spidattr => $moodlefield) {
            $attr = $doc->createElement('md:RequestedAttribute');
            $attr->setAttribute('Name', $spidattr);
            $attr->setAttribute('NameFormat', 'urn:oasis:names:tc:SAML:2.0:attrname-format:basic');
            $attr->setAttribute('isRequired', 'false');
            $attrconsuming->appendChild($attr);
        }

        return $doc->saveXML();
    }
}
