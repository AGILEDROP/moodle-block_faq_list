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
 * FAQ item form.
 *
 * File         faq_item_form.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_faq_list\forms;

use coding_exception;
use moodleform;

/**
 * FAQ item creation form.
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class faq_item_form extends moodleform {

    /**
     * Form definitions.
     *
     * @return void
     * @throws coding_exception
     */
    protected function definition() {
        $mform = $this->_form;

        // Existing faq_item id.
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // Question.
        $mform->addElement('text', 'question', get_string('label:faqquestion', 'block_faq_list'));
        $mform->addRule('question', get_string('error:required', 'block_faq_list'), 'required', null, 'client');
        $mform->addRule('question', get_string('error:required', 'block_faq_list'), 'required', null, 'client');
        $mform->setType('question', PARAM_TEXT);
        $mform->addHelpButton('question', 'label:faqquestion', 'block_faq_list');

        // Answer.
        $editordefinition = [
            'subdirs' => 0,
            'maxbytes' => 0,
            'maxfiles' => 0,
            'changeformat' => 1,
            'context' => null,
            'noclean' => 0,
            'trusttext' => 0,
            'enable_filemanagement' => true,
        ];
        $mform->addElement('editor', 'answer', get_string('label:faqanswer', 'block_faq_list'), null, $editordefinition);
        $mform->setType('answer', PARAM_RAW);
        $mform->addRule('answer', get_string('error:required', 'block_faq_list'), 'required', null, 'client');
        $mform->addRule('answer', get_string('error:required', 'block_faq_list'), 'required', null, 'client');
        $mform->addHelpButton('answer', 'label:faqanswer', 'block_faq_list');

        $this->add_action_buttons();
    }

    /**
     * Performs validation of the form information
     *
     * @param array $data
     * @param array $files
     * @return array An array of $errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}
