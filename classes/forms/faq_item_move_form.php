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
 * File         faq_item_move_form.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2025
 * @author      Agiledrop 2025 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_faq_list\forms;

use coding_exception;
use html_writer;
use moodleform;

/**
 * FAQ item creation form.
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2025
 * @author      Agiledrop 2025 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class faq_item_move_form extends moodleform {

    /**
     * Constructor.
     *
     * @param string $actionurl
     * @param array $customdata
     */
    public function __construct($actionurl = null, $customdata = null) {
        parent::__construct($actionurl, $customdata);
        if (!empty($customdata)) {
            $this->_customdata = $customdata;
        }
    }

    /**
     * Form definitions.
     *
     * @return void
     * @throws coding_exception
     */
    protected function definition() {
        global $OUTPUT;
        $mform = $this->_form;

        $mform->addElement('hidden', 'faq_item_id');
        $mform->setType('faq_item_id', PARAM_INT);
        $mform->setDefault('faq_item_id', $this->_customdata['faq_item_id'] ?? null);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);
        $mform->setDefault('action', $this->_customdata['action'] ?? null);

        $mform->addElement('hidden', 'sesskey');
        $mform->setType('sesskey', PARAM_RAW);
        $mform->setDefault('sesskey', sesskey());

        $iconname = ($this->_customdata['action'] === 'moveitemup') ? 't/up' : 't/down';
        $iconurl = $OUTPUT->image_url($iconname);
        $buttonhtml = html_writer::empty_tag('input', [
            'type' => 'image',
            'src' => $iconurl,
            'alt' => get_string('button:editfaqitem', 'block_faq_list'),
            'title' => get_string('button:editfaqitem', 'block_faq_list'),
            'class' => 'action-icon',
        ]);
        $mform->addElement('html', $buttonhtml);
    }
}
