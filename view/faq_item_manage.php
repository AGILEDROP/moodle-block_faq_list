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
 * Create new FAQ list item or edit existing one.
 *
 * File         faq_item_manage.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_faq_list\faq_item;
use block_faq_list\forms\faq_item_form;
use core\output\notification;

require('../../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/adminlib.php');

global $CFG, $USER, $DB, $OUTPUT, $PAGE;

$PAGE->set_url('/blocks/faq_list/view/faq_item_manage.php');

require_login();
$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('admin');

$faqitem = new faq_item();
$faqitemform = new faq_item_form();

$faqitemid = optional_param('faq_item_id', null, PARAM_INT);

$header = get_string('header:faqitemadd', 'block_faq_list');
$PAGE->set_title($header);
$PAGE->set_heading($header);

$redirecturl  = new moodle_url('/blocks/faq_list/view/faq_list_items.php', []);


if ($faqitemform->is_cancelled()) {
    // Redirect to list of faq lists.
    redirect($redirecturl);
} else if ($data = $faqitemform->get_data()) {
    // Validation.
    if ($data->id) {
        $faqitem->update($data);
        redirect($redirecturl,
            get_string('msgfaqitemupdated', 'block_faq_list'),
            0,
            notification::NOTIFY_SUCCESS);
    } else {
        $faqitem->create($data);
        redirect($redirecturl,
            get_string('msgfaqitemcreated', 'block_faq_list'),
            0,
            notification::NOTIFY_SUCCESS);
    }

}

if ($faqitemid) {
    $existingfaqitem = $faqitem->get_by_id($faqitemid);

    $data = new stdClass();
    $data->id = $existingfaqitem->id;
    $data->question = $existingfaqitem->question;
    $data->answer['text'] = $existingfaqitem->answer;
    $data->answer['format'] = $existingfaqitem->answer_format;
    if ($existingfaqitem) {
        $faqitemform->set_data($data);
    } else {
        redirect($redirecturl,
            get_string('msgfaqitemnotexist', 'block_faq_list'),
            0,
            notification::NOTIFY_ERROR
        );
    }
}


echo $OUTPUT->header();

$faqitemform->display();

echo $OUTPUT->footer();
