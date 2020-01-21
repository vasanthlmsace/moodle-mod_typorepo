<?php

/**
 * typorepo module main user interface
 *
 * @package    mod
 * @subpackage typorepo
 * @copyright  Learntube Team www.learntube.de  {@link www.learntube.de}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');

$id = optional_param('id', 0, PARAM_INT);
$t = optional_param('t' , 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$editing = optional_param('editing', 0, PARAM_BOOL);
$redirect = optional_param('redirect', 0, PARAM_BOOL);

if ($t) {
    if (!$typorepo = $DB->get_record('typorepo', ['id'=> $t])) {
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('typorepo', $typorepo->id, $typorepo->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('typorepo', $id)) {
        print_error('invalidcoursemodule');
    }
    $typorepo = $DB->get_record('typorepo', ['id' => $cm->instance], '*', MUST_EXIST);
}

$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/typorepo:view', $context);

// Completion and trigger events.
typorepo_view($typorepo, $course, $cm, $context);

// calculate the url
$time = time();
// $cm->id .
$token = MD5($USER->username . $USER->firstname . $USER->lastname . $course->id . $time . $USER->email .
    get_config('typorepo', 'secret'));
$fullurl = $typorepo->url  . '&token=' . $token . '&time=' . $time . '&moodlemodid=' . $cm->id . '&login=' .
    base64_encode($USER->username) . '&firstname=' .  base64_encode($USER->firstname) . '&lastname=' .
    base64_encode($USER->lastname) . '&courseid=' .  $course->id . '&email=' .  base64_encode($USER->email);

if ($redirect) {
    // Coming from course page or url index page.
    // This redirect trick solves caching problems when tracking views.
    redirect($fullurl);
}

$PAGE->set_url('/mod/typorepo/view.php', ['id' => $cm->id]);
$PAGE->set_title($course->shortname.': '.$typorepo->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($typorepo);

// calculate the url
$time = time();
// $cm->id .
$token = MD5($USER->username . $USER->firstname . $USER->lastname . $course->id . $time . $USER->email .
    get_config('typorepo', 'secret'));
$fullurl = $typorepo->url  . '&token=' . $token . '&time=' . $time . '&moodlemodid=' . $cm->id . '&login=' .
    base64_encode($USER->username) . '&firstname=' .  base64_encode($USER->firstname) . '&lastname=' .
    base64_encode($USER->lastname) . '&courseid=' .  $course->id . '&email=' .  base64_encode($USER->email);

echo $OUTPUT->header();

echo '<iframe style="margin-left: 0px;" src="' . $fullurl . '" frameborder="0" scrolling="' . get_config('typorepo', 'scrolling') . '" width="100%"  height="' . get_config('typorepo', 'height') . '"> </iframe>';
echo $OUTPUT->footer();

echo $OUTPUT->footer();