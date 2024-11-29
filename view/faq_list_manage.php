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
 * Create new FAQ list or edit existing one.
 *
 * File         faq_list_manage.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_faq_list\faq_list;
use block_faq_list\forms\faq_list_form;
use core\output\notification;

require('../../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/adminlib.php');

global $CFG, $USER, $DB, $OUTPUT, $PAGE;

$PAGE->set_url('/blocks/faq_list/view/faq_list_manage.php');

require_login(0, false);

$context = \context_system::instance();
require_capability('block/faq_list:createormodify', $context);

$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('admin');

$faqlist = new faq_list();
$faqlistform = new faq_list_form();


$action = optional_param('action', null, PARAM_ALPHA);
$faqlistid = optional_param('list_id', null, PARAM_INT);

$header = get_string('header:faqlistadd', 'block_faq_list');
$PAGE->set_title($header);
$PAGE->set_heading($header);

$redirecturl  = new moodle_url('/blocks/faq_list/view/faq_lists.php', []);


if ($faqlistform->is_cancelled()) {
    // Redirect to list of faq lists.
    redirect($redirecturl);
} else if ($data = $faqlistform->get_data()) {
    // Validation.
    if ($data->id) {
        $faqlist->update($data);
        redirect($redirecturl,
            get_string('msgfaqlistupdated', 'block_faq_list'),
            0,
            notification::NOTIFY_SUCCESS);
    } else {
        $faqlist->create($data);
        redirect($redirecturl,
            get_string('msgfaqlistcreated', 'block_faq_list'),
            0,
            notification::NOTIFY_SUCCESS);
    }

}

if ($faqlistid) {
    $existingfaqlist = $faqlist->get_by_id($faqlistid);

    if ($existingfaqlist) {
        $faqlistform->set_data($existingfaqlist);
    } else {
        redirect($redirecturl,
            get_string('msgfaqlistnotexist', 'block_faq_list'),
            0,
            notification::NOTIFY_ERROR
        );
    }
}

echo $OUTPUT->header();

$faqlistform->display();

echo $OUTPUT->footer();
