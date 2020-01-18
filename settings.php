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
 * typorepo module admin settings and defaults
 *
 * @package   mod_typorepo
 * @copyright 2020 bdecent gmbh <https://bdecent.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $PAGE;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('typorepo_display_settings',
        get_string('displaysettings', 'typorepo'), ''));

    $settings->add(new admin_setting_configtext('typorepo/width', get_string('width', 'typorepo'),
        get_string('widthdesc', 'typorepo'), '1000',  PARAM_INT, 10));

    $settings->add(new admin_setting_configtext('typorepo/height', get_string('height', 'typorepo'),
        get_string('heightdesc', 'typorepo'), '500',  PARAM_INT, 10));

    $settings->add(new admin_setting_configtext('typorepo/scrolling', get_string('scrolling', 'typorepo'),
        get_string('scrollingdesc', 'typorepo'), 'auto',  PARAM_URL, 20));

    $settings->add(new admin_setting_heading('typorepo_integration_settings',
        get_string('integrationsettings', 'typorepo'), ''));

    $settings->add(new admin_setting_configtext('typorepo/url', get_string('url', 'typorepo'),
        get_string('urldesc', 'typorepo'), '',  PARAM_URL, 60));

    $settings->add(new admin_setting_configtext('typorepo/pageurl', get_string('typopageurl', 'typorepo'),
        get_string('typopageurldesc', 'typorepo'), '',  PARAM_URL, 60));

    $settings->add(new admin_setting_configtext('typorepo/secret', get_string('typosecret', 'typorepo'),
        get_string('typosecretdesc', 'typorepo'), '',  PARAM_URL, 60));
}
