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
 * Manage FAQ list title translation.
 *
 * File         faq_list_title.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_faq_list\faq_list;
use block_faq_list\forms\faq_list_title_form;
use core\output\notification;

require('../../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/adminlib.php');

global $CFG, $USER, $DB, $OUTPUT, $PAGE;

$PAGE->set_url('/blocks/faq_list/view/faq_list_title.php');

require_login(0, false);

$context = \context_system::instance();
require_capability('block/faq_list:createormodify', $context);

$PAGE->set_context(context_system::instance());

$PAGE->set_pagelayout('admin');

$faqlist = new faq_list();
$faqtitleid = optional_param('faq_title_id', 0, PARAM_INT) ?: optional_param('id', 0, PARAM_INT);
$listsurl = new moodle_url('/blocks/faq_list/view/faq_lists.php');

if ($faqtitleid) {
    $existingfaqtitle = $faqlist->get_title_by_id($faqtitleid);
    if (!$existingfaqtitle) {
        redirect($listsurl, get_string('msgfaqtitlenotexist', 'block_faq_list'), 0, notification::NOTIFY_ERROR);
    }
    $listid = (int)$existingfaqtitle->list_id;
    $lang = $existingfaqtitle->lang;
} else {
    $listid = optional_param('list_id', 0, PARAM_INT);
    $lang = optional_param('faq_lang', '', PARAM_ALPHANUMEXT);
}

if (!$listid || !$faqlist->get_by_id($listid)) {
    redirect($listsurl, get_string('msgfaqlistnotexist', 'block_faq_list'), 0, notification::NOTIFY_ERROR);
}

$redirecturl = new moodle_url('/blocks/faq_list/view/faq_list_items.php', [
    'list_id' => $listid,
    'faq_lang' => $lang,
]);
$faqtitleform = new faq_list_title_form();
$faqtitleform->set_data([
    'id' => $faqtitleid,
    'title' => $existingfaqtitle->title ?? '',
    'list_id' => $listid,
    'faq_lang' => $lang,
]);

$header = get_string('header:faqtitlemanagement', 'block_faq_list');
$PAGE->set_title($header);
$PAGE->set_heading($header);

if ($faqtitleform->is_cancelled()) {
    redirect($redirecturl);
} else if ($data = $faqtitleform->get_data()) {
    try {
        if ($faqtitleid) {
            // The stored record decides which title is saved, not the submitted hidden values.
            $faqlist->update_title_translation($faqtitleid, $data->title);
            $message = get_string('msgfaqtitleupdated', 'block_faq_list');
        } else {
            $faqlist->add_title_translation($data->list_id, $data->title, $data->faq_lang);
            $message = get_string('msgfaqtitlecreated', 'block_faq_list');
        }
    } catch (moodle_exception $e) {
        $expectederrors = ['error:titleexists', 'error:locktimeout', 'msgfaqlistnotexist', 'msgfaqtitlenotexist'];
        if ($e->module !== 'block_faq_list' || !in_array($e->errorcode, $expectederrors, true)) {
            throw $e;
        }
        redirect(
            in_array($e->errorcode, ['msgfaqlistnotexist', 'msgfaqtitlenotexist']) ? $listsurl : $redirecturl,
            $e->getMessage(),
            0,
            notification::NOTIFY_ERROR
        );
    }
    redirect($redirecturl, $message, 0, notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

$faqtitleform->display();

echo $OUTPUT->footer();
