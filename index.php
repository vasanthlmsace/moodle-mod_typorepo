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
 * List typorepo instances in a course.
 *
 * @package   mod_typorepo
 * @copyright 2020 bdecent gmbh <https://bdecent.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once("../../config.php");

$id = required_param('id', PARAM_INT); // Course ID.

if (!$course = $DB->get_record('course', ['id' => $id])) {
    print_error('invalidcourseid');
}

$strintro = get_string('moduleintro');
$strlastmodified = get_string("lastmodified");
$strname = get_string("name");
$strsectionname = get_string('sectionname', 'format_'.$course->format);
$strsummary = get_string("summary");
$strtyporepo = get_string("modulename", "typorepo");
$strtyporepos = get_string("modulenameplural", "typorepo");

$PAGE->set_pagelayout('incourse');
$PAGE->set_url(new moodle_url('/mod/typorepo/index.php', ['id' => $id]));
$PAGE->navbar->add($strtyporepos);
$PAGE->set_title($strtyporepos);
$PAGE->set_heading($course->fullname);

require_course_login($course, true);

$params = [
    'context' => context_course::instance($course->id),
    'courseid' => $course->id
];
$event = \mod_typorepo\event\course_module_instance_list_viewed::create($params);
$event->trigger();

echo $OUTPUT->header();
echo $OUTPUT->heading($strtyporepos);

if (!$typorepos = get_all_instances_in_course('typorepo', $course)) {
    notice(get_string('thereareno', 'moodle', $strtyporepos),
        new moodle_url('/course/view.php', ['id' => $course->id]));
    exit;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $table->head  = [$strsectionname, $strname, $strintro];
    $table->align = ['center', 'left', 'left'];
} else {
    $table->head  = [$strlastmodified, $strname, $strintro];
    $table->align = ['left', 'left', 'left'];
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($typorepos as $typorepo) {
    $cm = $modinfo->cms[$typorepo->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($typorepo->section !== $currentsection) {
            if ($typorepo->section) {
                $printsection = get_section_name($course, $typorepo->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $typorepo->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($typorepo->timemodified)."</span>";
    }

    $extra = empty($cm->extra) ? '' : $cm->extra;
    $icon = '';
    if (!empty($cm->icon)) {
        // Each resource file has an icon in 2.0.
        $icon = $OUTPUT->pix_icon($cm->icon, get_string('modulename', $cm->modname));
    }

    $class = $typorepo->visible ? '' : 'class="dimmed"'; // Hidden modules are dimmed.
    $table->data[] = [
        $printsection,
        "<a $class $extra href=\"view.php?id=$cm->id\">".$icon.format_string($typorepo->name)."</a>",
        format_module_intro('typorepo', $typorepo, $cm->id)];
}

echo html_writer::table($table);

echo $OUTPUT->footer();