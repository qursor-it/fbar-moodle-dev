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

/**
 * Language strings.
 *
 * @package     auth_spid
 * @copyright   2025 Finzioni
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'SPID authentication';
$string['auth_spiddescription'] = 'Provides SPID authentication via the auth_saml2 plugin and dedicated SPID configuration.';
$string['settings:generalheading'] = 'SPID configuration';
$string['settings:entityid'] = 'Entity ID';
$string['settings:entityid_help'] = 'Identifier of the Service Provider registered with SPID.';
$string['settings:acs'] = 'Assertion Consumer Service URL';
$string['settings:slo'] = 'Single Logout URL';
$string['settings:metadata'] = 'Metadata endpoint';
$string['settings:certduration'] = 'Certificate validity (days)';
$string['settings:certpath'] = 'Certificate storage path';
$string['settings:attrmap'] = 'Attribute mapping (JSON)';
$string['settings:attrmap_help'] = 'JSON object mapping SPID attributes (es. "spidCode") to Moodle user fields (es. "idnumber").';
$string['settings:autoupdate'] = 'Automatically refresh metadata';
$string['settings:autoupdate_desc'] = 'If enabled, metadata will be periodically regenerated using the scheduled task.';
$string['settings:buttonlabel'] = 'Login button label';
$string['settings:debug'] = 'Enable SPID debug logging';
$string['settings:certificate'] = 'SPID certificate (PEM)';
$string['settings:certificate_help'] = 'Paste the X.509 certificate provided for the SPID Service Provider in PEM format.';
$string['settings:privatekey'] = 'SPID private key (PEM)';
$string['settings:privatekey_help'] = 'Paste the private key associated with the SPID certificate in PEM format. Store securely.';
$string['settings:metadatadownload'] = 'Download metadata';
$string['settings:metadatadownload_desc'] = 'Use this link to download the SPID metadata exposed by this plugin.';
$string['event:loginstarted'] = 'SPID login started';
$string['event:loginfailed'] = 'SPID login failed';
$string['event:loginsuccess'] = 'SPID login successful';
$string['privacy:metadata'] = 'The SPID authentication plugin stores configuration and metadata necessary to interact with the SPID federation.';
$string['error:missingdependency'] = 'The auth_saml2 plugin is not installed or not available. Install and enable it before using SPID authentication.';
$string['error:missingvendor'] = 'The SPID PHP library is not available. Run composer install inside auth/spid to install dependencies.';
$string['cli:regenerated'] = 'SPID metadata regenerated in {$a}.';
$string['cli:regenerateerror'] = 'Unable to regenerate SPID metadata: {$a}';
$string['task:metadatarefresh'] = 'Regenerate SPID metadata';
$string['button:login'] = 'Entra con SPID';
$string['metadata:signaturemissing'] = 'Metadata signature generation failed because the certificate or private key is missing.';
$string['metadata:generated'] = 'SPID metadata generated successfully.';
$string['metadata:regenerated'] = 'Metadata regenerated and cached.';
