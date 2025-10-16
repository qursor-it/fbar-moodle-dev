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

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    require_once(__DIR__ . '/lib.php');

    $settings->add(new admin_setting_heading('auth_spid/general', get_string('settings:generalheading', 'auth_spid'),
        get_string('auth_spiddescription', 'auth_spid')));

    $settings->add(new admin_setting_configtext('auth_spid/entityid', get_string('settings:entityid', 'auth_spid'),
        get_string('settings:entityid_help', 'auth_spid'), '', PARAM_RAW_TRIMMED));

    $settings->add(new admin_setting_configtext('auth_spid/acs', get_string('settings:acs', 'auth_spid'), '', '', PARAM_URL));

    $settings->add(new admin_setting_configtext('auth_spid/slo', get_string('settings:slo', 'auth_spid'), '', '', PARAM_URL));

    $settings->add(new admin_setting_configtext('auth_spid/certduration', get_string('settings:certduration', 'auth_spid'), '', 365, PARAM_INT));

    $settings->add(new admin_setting_configtext('auth_spid/certpath', get_string('settings:certpath', 'auth_spid'), '', '', PARAM_RAW_TRIMMED));

    $settings->add(new admin_setting_configtextarea('auth_spid/attrmap', get_string('settings:attrmap', 'auth_spid'),
        get_string('settings:attrmap_help', 'auth_spid'), json_encode([
            'spidCode' => 'idnumber',
            'name' => 'firstname',
            'familyName' => 'lastname',
            'email' => 'email',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)));

    $settings->add(new admin_setting_configcheckbox('auth_spid/autoupdate', get_string('settings:autoupdate', 'auth_spid'),
        get_string('settings:autoupdate_desc', 'auth_spid'), 1));

    $settings->add(new admin_setting_configtext('auth_spid/buttonlabel', get_string('settings:buttonlabel', 'auth_spid'), '',
        get_string('button:login', 'auth_spid'), PARAM_TEXT));

    $settings->add(new admin_setting_configcheckbox('auth_spid/debug', get_string('settings:debug', 'auth_spid'), '', 0));

    $settings->add(new admin_setting_configtextarea('auth_spid/certificate', get_string('settings:certificate', 'auth_spid'),
        get_string('settings:certificate_help', 'auth_spid'), '', PARAM_RAW));

    $settings->add(new admin_setting_configtextarea('auth_spid/privatekey', get_string('settings:privatekey', 'auth_spid'),
        get_string('settings:privatekey_help', 'auth_spid'), '', PARAM_RAW));

    $metadataurl = (new moodle_url('/auth/spid/metadata.php'))->out();
    $settings->add(new admin_setting_heading('auth_spid/metadata', get_string('settings:metadatadownload', 'auth_spid'),
        html_writer::link($metadataurl, $metadataurl, ['target' => '_blank']) . html_writer::empty_tag('br') .
        get_string('settings:metadatadownload_desc', 'auth_spid')));
}
