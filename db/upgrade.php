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
 * Upgrades to database.
 *
 * @package   mod_typorepo
 * @copyright 2020 bdecent gmbh <https://bdecent.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrades to database.
 *
 * @param int $oldversion
 * @return bool
 * @throws ddl_change_structure_exception
 * @throws ddl_exception
 * @throws ddl_table_missing_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_typorepo_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020011700) {

        // Define field timecreated to be added to typorepo.
        $table = new xmldb_table('typorepo');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timemodified');

        // Conditionally launch add field timecreated.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field intro to be added to typorepo.
        $table = new xmldb_table('typorepo');
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, null, null, false, null, null, 'timecreated');

        // Conditionally launch add field intro.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field introformat to be added to typorepo.
        $table = new xmldb_table('typorepo');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'intro');

        // Conditionally launch add field introformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index course (not unique) to be added to typorepo.
        $table = new xmldb_table('typorepo');
        $index = new xmldb_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);

        // Conditionally launch add index course.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Typorepo savepoint reached.
        upgrade_mod_savepoint(true, 2020011700, 'typorepo');
    }

    if ($oldversion < 2020011701) {

        // Define field label_mode to be added to typorepo.
        $table = new xmldb_table('typorepo');
        $field = new xmldb_field('label_mode', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'introformat');

        // Conditionally launch add field label_mode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Typorepo savepoint reached.
        upgrade_mod_savepoint(true, 2020011701, 'typorepo');
    }

    return true;
}
