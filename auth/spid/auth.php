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

namespace auth_spid;

use auth_plugin_base;
use auth_spid\api\spid_service;
use auth_spid\certificates\generator;
use auth_spid\certificates\repository;
use auth_spid\metadata\builder;
use auth_spid\util\config as config_util;
use core\notification;
use html_writer;
use moodle_url;
use stdClass;

/**
 * SPID authentication plugin built on top of auth_saml2 configuration.
 *
 * @package     auth_spid
 */
class auth extends auth_plugin_base {
    /** @var config_util */
    protected config_util $configutil;

    /** @var spid_service */
    protected spid_service $service;

    /** @var \auth_plugin_saml2|null */
    protected ?\auth_plugin_saml2 $delegate = null;

    /**
     * Constructor.
     */
    public function __construct() {
        global $CFG;
        require_once($CFG->dirroot . '/auth/spid/lib.php');
        auth_spid_load_vendor();

        parent::__construct('spid');
        $this->configutil = new config_util();
        $repository = new repository($this->configutil);
        $generator = new generator($repository, $this->configutil);
        $builder = new builder($this->configutil, $generator);
        $this->service = new spid_service($this->configutil, $builder, $generator);

        if (file_exists($CFG->dirroot . '/auth/saml2/auth.php')) {
            require_once($CFG->dirroot . '/auth/saml2/auth.php');
        }

        if (class_exists('auth_plugin_saml2')) {
            $this->delegate = new \auth_plugin_saml2();
        }
    }

    public function user_login($username, $password) {
        // SPID is SSO based and does not support password login.
        return false;
    }

    public function prevent_local_passwords() {
        return true;
    }

    public function can_change_password() {
        return false;
    }

    public function can_reset_password() {
        return false;
    }

    public function can_signup() {
        return false;
    }

    public function is_internal() {
        return false;
    }

    /**
     * Add the SPID login button to the login page.
     */
    public function loginpage_hook() {
        global $CFG, $PAGE, $OUTPUT;

        if (!empty($CFG->alternateloginurl)) {
            return;
        }

        if (!$this->delegate) {
            if (is_siteadmin()) {
                core\notification::add(get_string('error:missingdependency', 'auth_spid'), core\notification::ERROR);
            }
            return;
        }

        if (!class_exists('SPID_PHP\\SPID_CIE\\Authn\\AuthnRequest')) {
            if (is_siteadmin()) {
                core\notification::add(get_string('error:missingvendor', 'auth_spid'), core\notification::WARNING);
            }
            return;
        }

        $label = $this->configutil->get('buttonlabel', get_string('button:login', 'auth_spid'));
        $target = new moodle_url('/auth/saml2/login.php', [
            'wantsurl' => optional_param('wantsurl', qualified_me(), PARAM_RAW),
            'idp' => 'spid',
        ]);

        $html = html_writer::div(
            html_writer::link($target, $label, ['class' => 'btn btn-primary btn-lg auth-spid-button']),
            'auth-spid-container mt-3'
        );

        $PAGE->requires->css('/auth/spid/styles.css');
        echo $html;
    }

    /**
     * Return custom login URL when triggered programmatically.
     */
    public function get_login_url() {
        return new moodle_url('/auth/saml2/login.php', ['idp' => 'spid']);
    }

    /**
     * Process SPID attributes and update user fields.
     *
     * @param stdClass $user Moodle user.
     * @param array $attributes Attributes from SPID response.
     */
    public function update_user_fields(stdClass $user, array $attributes): stdClass {
        $mapping = $this->configutil->get_attribute_mapping();
        foreach ($mapping as $spidattr => $field) {
            if (isset($attributes[$spidattr]) && property_exists($user, $field)) {
                $value = $attributes[$spidattr];
                if (is_array($value)) {
                    $value = reset($value);
                }
                $user->{$field} = $value;
            }
        }
        return $user;
    }

    /**
     * Provide metadata as helper for other components.
     */
    public function get_metadata(): string {
        return $this->service->get_metadata();
    }

    /**
     * Expose certificate pair.
     */
    public function get_certificate_pair(): array {
        return $this->service->get_certificate_pair();
    }
}
