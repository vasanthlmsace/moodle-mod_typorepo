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
 * Helper class for typo3 operations.
 *
 * @package   mod_typorepo
 * @copyright 2020 bdecent gmbh <https://bdecent.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_typorepo;

defined('MOODLE_INTERNAL') || die;

/**
 * Helper class for typo3 operations.
 *
 * @package mod_typorepo
 */
class typo3 {

    /**
     * Build and return typo3 URL.
     *
     * @param string $courseid
     * @param string $updateid
     * @param string $baseurl
     * @return string
     * @throws \dml_exception
     */
    public static function build_url($courseid = '', $updateid = '', $baseurl = null) {
        global $USER;
        $time = time();
        $token = md5($updateid .
            $USER->id .
            $USER->id .
            $USER->id .
            $courseid .
            $time .
            $USER->id .
            get_config('typorepo', 'secret'));

        if (!$baseurl) {
            $baseurl = get_config('typorepo', 'url');
        }

        return $baseurl .
            '&token=' . $token .
            '&time=' . $time .
            '&moodlemodid=' . $updateid .
            '&login=' . base64_encode($USER->id) .
            '&firstname=' .  base64_encode($USER->id) .
            '&lastname=' .  base64_encode($USER->id) .
            '&courseid=' .  $courseid .
            '&email=' .  base64_encode($USER->id);
    }
}
