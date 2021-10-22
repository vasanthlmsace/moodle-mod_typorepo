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
 * Generate the selected typo3 page tree url and update to Form hidden url.
 *
 * @package   mod_typorepo
 * @copyright 2020 bdecent gmbh <https://bdecent.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');

$context = context_system::instance();
$PAGE->set_context($context);

require_login();

$language = optional_param("L", '', PARAM_TEXT);
$pageid = required_param("pageid", PARAM_INT);
$language  = urldecode($language);
$url       = get_config('typorepo', 'pageurl').urldecode($pageid).'&L='.$language;
$linksaved = get_string('linksaved', 'typorepo');
$templatecontent = [];
$templatecontent['url'] = $url;
$templatecontent['linksaved'] = $linksaved;
// Return the html and js content to save the typo3 tree url.
echo $OUTPUT->render_from_template('mod_typorepo/save', $templatecontent);

