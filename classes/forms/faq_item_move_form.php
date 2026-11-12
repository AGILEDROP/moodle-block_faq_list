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
     * Unique identifier per rendered form.
     *
     * The list page renders one move form per item and direction. The default identifier is the class
     * name, so every form would share the same submission marker field and form name. Appending the
     * action and item id makes each one unique. Custom data is already set when this is called.
     *
     * @return string
     */
    protected function get_form_identifier() {
        $suffix = ($this->_customdata['action'] ?? '') . '_' . ($this->_customdata['faq_item_id'] ?? '');
        return parent::get_form_identifier() . '_' . preg_replace('/[^a-z0-9_]/i', '_', $suffix);
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

        $iconname = ($this->_customdata['action'] === 'moveitemup') ? 't/up' : 't/down';
        $iconurl = $OUTPUT->image_url($iconname);
        $buttonhtml = html_writer::empty_tag('input', [
            'type' => 'image',
            'src' => $iconurl,
            'alt' => get_string('button:editfaqitem', 'block_faq_list'),
            'title' => get_string('button:editfaqitem', 'block_faq_list'),
            'class' => 'action-icon',
            'id' => $this->get_form_identifier() . '_button',
        ]);
        $mform->addElement('html', $buttonhtml);
    }
}
