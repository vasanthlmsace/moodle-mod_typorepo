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
 * @package   mod_typorepo
 * @copyright 2020 bdecent gmbh <https://bdecent.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . '/filelib.php');

defined('MOODLE_INTERNAL') || die();

/**
 * List of features supported in typorepo module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function typorepo_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * List of view style log actions
 * @return array
 */
function typorepo_get_view_actions() {
    return ['view', 'view all'];
}

/**
 * List of update style log actions
 * @return array
 */
function typorepo_get_post_actions() {
    return ['update', 'add'];
}

/**
 * Add new instance of typorepo.
 *
 * @param \stdClass $instance
 * @return bool|int
 * @throws dml_exception
 */
function typorepo_add_instance($instance) {
    global $DB;

    $instance->timemodified = time();
    $instance->timecreated = time();

    return $DB->insert_record('typorepo', $instance);
}

/**
 * @param \stdClass $instance
 * @return bool
 * @throws dml_exception
 */
function typorepo_update_instance($instance) {
    global $DB;

    $instance->id = $instance->instance;
    $instance->timemodified = time();

    return $DB->update_record('typorepo', $instance);
}

/**
 * @param $id
 * @return bool
 * @throws dml_exception
 */
function typorepo_delete_instance($id) {
    global $DB;

    if (!$instance = $DB->get_record('typorepo', ['id' => $id])) {
        return false;
    }

    return $DB->delete_records('typorepo', ['id' => $instance->id]);
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $typorepo   typorepo object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @throws coding_exception
 * @since Moodle 3.0
 */
function typorepo_view($typorepo, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = [
        'context' => $context,
        'objectid' => $typorepo->id
    ];

    $event = \mod_typorepo\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('typorepo', $typorepo);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Sets dynamic information about a course module
 *
 * This function is called from cm_info when displaying the module
 * mod_folder can be displayed inline on course page and therefore have no course link
 *
 * @param cm_info $cm
 * @throws dml_exception
 * @throws coding_exception
 */
function typorepo_cm_info_dynamic(cm_info $cm) {
    global $PAGE, $DB, $USER, $OUTPUT;

    // Ensure we are on the course view page. This was throwing an error when viewing the module
    // because OUTPUT was being used.
    if (!$PAGE->context || $PAGE->context->contextlevel != CONTEXT_COURSE) {
        return;
    }

    $instance = $DB->get_record('typorepo', ['id' => $cm->instance], '*', MUST_EXIST);

    if ($instance->label_mode) {

        // Calculate the url.
        $time = time();
        $token = MD5($USER->username . $USER->firstname . $USER->lastname . $cm->course . $time . $USER->email .
            get_config('typorepo', 'secret'));
        $fullurl = $instance->url  . '&token=' . $token . '&time=' . $time . '&moodlemodid=' . $cm->id . '&login=' .
            base64_encode($USER->username) . '&firstname=' .  base64_encode($USER->firstname) . '&lastname=' .
            base64_encode($USER->lastname) . '&courseid=' .  $cm->course . '&email=' .  base64_encode($USER->email);
        $iframe = '<iframe style="margin-left: 0px;" src="' . $fullurl . '" frameborder="0" scrolling="' .
            get_config('typorepo', 'scrolling') . '" width="100%"  height="' .
            get_config('typorepo', 'height') . '"> </iframe>';

        $content = $OUTPUT->render_from_template('mod_typorepo/view', [
            'instance' => $instance,
            'cmid' => $cm->id,
            'iframe' => $iframe
        ]);

        typorepo_view($instance, $PAGE->course, $cm, context_module::instance($cm->id));

        $cm->set_no_view_link();
        $cm->set_extra_classes('label_mode');
        $cm->set_content($content);
    }
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param stdClass $coursemodule
 * @return cached_cm_info info
 */
function typorepo_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;

    if (!$typorepo = $DB->get_record('typorepo', ['id' => $coursemodule->instance])) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $typorepo->name;
    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('typorepo', $typorepo, $coursemodule->id, false);
    }

    if ($typorepo->options == 'newwindow') {
        $fullurl = "$CFG->wwwroot/mod/typorepo/view.php?id=$coursemodule->id&amp;redirect=1";
        $width  = get_config('typorepo', 'width');
        $height = get_config('typorepo', 'height');
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $info->onclick = "window.open('$fullurl', '', '$wh'); return false;";
    }

    return $info;
}