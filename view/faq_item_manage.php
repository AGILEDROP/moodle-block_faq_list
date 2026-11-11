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
use block_faq_list\faq_list;
use block_faq_list\forms\faq_item_form;
use core\output\notification;

require('../../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/adminlib.php');

global $CFG, $USER, $DB, $OUTPUT, $PAGE;

$PAGE->set_url('/blocks/faq_list/view/faq_item_manage.php');

require_login(0, false);

$context = \context_system::instance();
require_capability('block/faq_list:createormodify', $context);

$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('admin');

$faqitem = new faq_item();

$faqitemid = optional_param('faq_item_id', 0, PARAM_INT) ?: optional_param('id', 0, PARAM_INT);
$listsurl = new moodle_url('/blocks/faq_list/view/faq_lists.php');

// The target list and language come from the stored question when editing, or from the page
// that opened the form when adding. They are never taken from the session on save.
if ($faqitemid) {
    $existingfaqitem = $faqitem->get_by_id($faqitemid);
    if (!$existingfaqitem) {
        redirect(
            new moodle_url('/blocks/faq_list/view/faq_list_items.php'),
            get_string('msgfaqitemnotexist', 'block_faq_list'),
            0,
            notification::NOTIFY_ERROR
        );
    }
    $listid = (int)$existingfaqitem->list_id;
    $lang = $existingfaqitem->lang;
} else {
    $listid = optional_param('list_id', 0, PARAM_INT);
    $lang = optional_param('faq_lang', '', PARAM_ALPHANUMEXT);
}

if (!$listid || !(new faq_list())->get_by_id($listid)) {
    redirect($listsurl, get_string('msgfaqlistnotexist', 'block_faq_list'), 0, notification::NOTIFY_ERROR);
}

$redirecturl = new moodle_url('/blocks/faq_list/view/faq_list_items.php', [
    'list_id' => $listid,
    'faq_lang' => $lang,
]);

$faqitemform = new faq_item_form();
$formdata = [
    'id' => $faqitemid,
    'list_id' => $listid,
    'faq_lang' => $lang,
];
if (!empty($existingfaqitem)) {
    $formdata['question'] = $existingfaqitem->question;
    $formdata['answer'] = [
        'text' => $existingfaqitem->answer,
        'format' => $existingfaqitem->answer_format,
    ];
}
$faqitemform->set_data($formdata);

$header = get_string('header:faqitemadd', 'block_faq_list');
$PAGE->set_title($header);
$PAGE->set_heading($header);

if ($faqitemform->is_cancelled()) {
    redirect($redirecturl);
} else if ($data = $faqitemform->get_data()) {
    try {
        if ($faqitemid) {
            // The stored record decides which item is saved, not the submitted hidden values.
            $data->id = $faqitemid;
            $faqitem->update($data);
            $message = get_string('msgfaqitemupdated', 'block_faq_list');
        } else {
            $faqitem->create($data);
            $message = get_string('msgfaqitemcreated', 'block_faq_list');
        }
    } catch (moodle_exception $e) {
        $expectederrors = ['error:locktimeout', 'msgfaqlistnotexist', 'msgfaqitemnotexist'];
        if ($e->module !== 'block_faq_list' || !in_array($e->errorcode, $expectederrors, true)) {
            throw $e;
        }
        redirect(
            $e->errorcode === 'msgfaqlistnotexist' ? $listsurl : $redirecturl,
            $e->getMessage(),
            0,
            notification::NOTIFY_ERROR
        );
    }
    redirect($redirecturl, $message, 0, notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

$faqitemform->display();

echo $OUTPUT->footer();
