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

use mod_typorepo\typo3;

defined('MOODLE_INTERNAL') || die();

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance edit form.
 */
class mod_typorepo_mod_form extends moodleform_mod {

    /**
     * Define form elements.
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    function definition() {

        $mform =& $this->_form;

        $mform->addElement('hidden', 'url');
        $mform->setType('url', PARAM_URL);

        $typo3url = typo3::build_url(optional_param('course', '', PARAM_INT), optional_param('update', '', PARAM_INT));

        $iframe = \html_writer::tag('iframe', '', [
            'src' => $typo3url,
            'frameborder' => 0,
            'scrolling' => get_config('typorepo', 'scrolling'),
            'width' => get_config('typorepo', 'width'),
            'height' => get_config('typorepo', 'height'),
            'class' => 'typo-embed'
        ]);
        $mform->addElement('html', $iframe);

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('advcheckbox', 'label_mode', get_string('embedincourse', 'typorepo'));
        $mform->setType('label_mode', PARAM_INT);
        $mform->addHelpButton('label_mode', 'embedincourse', 'typorepo');

        $woptions = [
            'newwindow' => get_string('newwindow', 'typorepo'),
            'iframe' => get_string('iframe', 'typorepo')
        ];
        $mform->addElement('select', 'options', get_string('displaysettings', 'typorepo'), $woptions);
        $mform->disabledIf('options', 'label_mode', 'checked');

        $this->standard_intro_elements();

        $features = [
            'groups' => false,
            'groupings' => false,
            'groupmembersonly' => false,
            'outcomes' => false,
            'gradecat' => false,
            'idnumber' => true
        ];
        $this->standard_coursemodule_elements($features);

        $this->add_action_buttons();
    }
}

