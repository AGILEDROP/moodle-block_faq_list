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
 * Delete existing FAQ list item.
 *
 * File         faq_item_delete.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_faq_list\faq_item;
use block_faq_list\forms\faq_item_delete_form;

require('../../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/adminlib.php');

global $CFG, $USER, $DB, $OUTPUT, $PAGE;

$PAGE->set_url('/blocks/faq_list/view/faq_item_delete.php');

require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');

$faqitem = new faq_item();

$faqitemid = optional_param('faq_item_id', null, PARAM_INT);

$redirecturl  = new moodle_url('/blocks/faq_list/view/faq_list_items.php', []);

if ($faqitemid) {
    $faqitemdata = $faqitem->get_by_id($faqitemid);
    $faqitemdeleteform = new faq_item_delete_form();
    $faqitemdeleteform->set_data($faqitemdata);
} else {
    $faqitemdeleteform = new faq_item_delete_form();
    if ($faqitemdeleteform->is_cancelled()) {
        redirect($redirecturl);
    }
}

if ($data = $faqitemdeleteform->get_data()) {
    // Validation.
    if ($data->id) {
        $faqitem->delete($data->id);
        redirect($redirecturl,
            get_string('msgfaqitemdeleted', 'block_faq_list'),
            0,
            'info');
    } else {
        redirect($redirecturl,
            get_string('msgfaqitemnotexist', 'block_faq_list'),
            0,
            'error');
    }
}

$header = get_string('header:faqitemdelete', 'block_faq_list');
$PAGE->set_title($header);
$PAGE->set_heading($header);

echo $OUTPUT->header();

$faqitemdeleteform->display();

echo $OUTPUT->footer();
