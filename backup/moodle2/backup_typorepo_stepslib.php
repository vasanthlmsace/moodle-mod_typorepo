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
 * Backup steps.
 *
 * @package   mod_typorepo
 * @copyright 2020 bdecent gmbh <https://bdecent.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define all the backup steps that will be used by the backup_typorepo_activity_task
 *
 * Define the complete typorepo structure for backup, with file and id annotations
 */
class backup_typorepo_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define structure of backup file.
     *
     * @return backup_nested_element
     * @throws base_element_struct_exception
     * @throws base_step_exception
     */
    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $typorepo = new backup_nested_element('typorepo', ['id'], [
            'course',
            'name',
            'url',
            'options',
            'timemodified'
        ]);

        // Define sources.
        $typorepo->set_source_table('typorepo', ['id' => backup::VAR_ACTIVITYID]);

        // Return the root element (typorepo), wrapped into standard activity structure.
        return $this->prepare_activity_structure($typorepo);
    }
}
