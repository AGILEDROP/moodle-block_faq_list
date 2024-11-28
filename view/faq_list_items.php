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
 * Display faq list items (questions and answers) for selected FAQ list.
 *
 * File         faq_list_items.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_faq_list\faq_item;

require('../../../config.php');


require_once($CFG->libdir . '/adminlib.php');
global $CFG, $USER, $DB, $OUTPUT, $PAGE;

$PAGE->set_url('/blocks/faq_list/view/faq_list_items.php');

require_login(0, false);

$context = \context_system::instance();
require_capability('block/faq_list:read', $context);

$PAGE->set_context(context_system::instance());

$faqitem = new faq_item();

$action = optional_param('action', null, PARAM_ALPHA);

$faqlistid = optional_param('list_id', null, PARAM_INT);
$faqlang = optional_param('faq_lang', null, PARAM_ALPHA);
$faqitemid = optional_param('faq_item_id', null, PARAM_INT);

if ($faqlistid) {
    $faqitem->helper->set_last_edit_faq_list_id($faqlistid);
}

if ($faqlang) {
    $faqitem->helper->set_last_edit_faq_lang($faqlang);
}

if ($action) {
    switch ($action) {
        case 'moveitemdown':
            if ($faqitemid) {
                $faqitem->movedown($faqitemid);
            };
            break;
        case 'moveitemup':
            if ($faqitemid) {
                $faqitem->moveup($faqitemid);
            }
            break;
    }
    redirect($PAGE->url);
}


$currentlistid = $faqitem->helper->get_last_edit_faq_list_id();

if (!$currentlistid) {
    $redirecturl = new moodle_url('/blocks/faq_list/view/faq_lists.php');
    redirect($redirecturl);
}

$currentfaqlang = $faqitem->helper->get_last_edit_faq_lang();
$currentfaqlisttitle = $faqitem->get_title($currentlistid, $currentfaqlang);

$PAGE->set_pagelayout('admin');

$header = get_string('header:faqitemlist', 'block_faq_list');
$PAGE->set_title($header);
$PAGE->set_heading($header);

echo $OUTPUT->header();

$table = new html_table();

$table->head = [
    get_string('label:colfaqitem', 'block_faq_list'),
    '', // Move down.
    '', // Move up.
    get_string('label:colactionedit', 'block_faq_list'),
    get_string('label:colactiondelete', 'block_faq_list'),
];

$rows = [];
$records = $faqitem->get_items($currentlistid, $currentfaqlang);
$faqitemscount = count($records);
$i = 0;

foreach ($records as $id => $record) {
    $i++;

    $movedownicon = '';
    $moveupicon = '';

    $urlparam = [
        'faq_item_id' => $record->id,
    ];

    $viewurl = new moodle_url('/blocks/faq_list/view/faq_item_manage.php', $urlparam);

    if ($i < $faqitemscount) {
        // Link to move down item.
        $params = array_merge($urlparam, ['action' => 'moveitemdown']);
        $movedownlink = new moodle_url($PAGE->url, $params);
        $movedownicon = $OUTPUT->action_icon($movedownlink,
                new \pix_icon('t/down', get_string('button:editfaqitem', 'block_faq_list')));
    }

    if ($i > 1) {
        // Link to move down item.
        $params = array_merge($urlparam, ['action' => 'moveitemup']);
        $moveuplink = new moodle_url($PAGE->url, $params);
        $moveupicon = $OUTPUT->action_icon($moveuplink,
                new \pix_icon('t/up', get_string('button:editfaqitem', 'block_faq_list')));
    }

    // Link to edit faq item.
    $editlink = new moodle_url('/blocks/faq_list/view/faq_item_manage.php', $urlparam);
    $editicon = $OUTPUT->action_icon($editlink,
            new \pix_icon('t/edit', get_string('button:editfaqitem', 'block_faq_list')));

    // Link to delete faq item.
    $params = array_merge($urlparam, ['action' => 'deleteitem']);
    $deletelink = new moodle_url('/blocks/faq_list/view/faq_item_delete.php', $params);
    $deleteicon = $OUTPUT->action_icon($deletelink,
            new \pix_icon('t/delete', get_string('button:deletefaqitem', 'block_faq_list')));

    $faqitemtext = '';
    $faqitemtext .= html_writer::tag('h5', $record->question, []);
    $faqitemtext .= html_writer::tag('div', $record->answer, []);

    $row = [
        $faqitemtext,
        $movedownicon,
        $moveupicon,
        $editicon,
        $deleteicon,
    ];
    $rows[] = $row;
}

$table->data = $rows;

$options = $faqitem->get_available_faq_list_dropdown_options();
echo $OUTPUT->single_select($PAGE->url, 'list_id', $options, $currentlistid);
echo html_writer::tag('hr', '', []);

$faqtitletext = get_string('header:faqtitle', 'block_faq_list');
$faqtitletext .= ': ';
if ($currentfaqlisttitle) {
    $faqtitletext .= $currentfaqlisttitle->title;
} else {
    $faqtitletext .= '/';
}

echo html_writer::start_div('d-flex');
echo html_writer::tag('h2', $faqtitletext, []);
echo html_writer::start_div('ml-2');
if ($currentfaqlisttitle) {
    $actionurl = new moodle_url('/blocks/faq_list/view/faq_list_title.php', ['faq_title_id' => $currentfaqlisttitle->id]);
    echo $OUTPUT->single_button($actionurl,
            get_string('button:editfaqtitle', 'block_faq_list'), 'POST', ['type' => 'secondary']);

} else {
    $actionurl = new moodle_url('/blocks/faq_list/view/faq_list_title.php');
    echo $OUTPUT->single_button($actionurl,
            get_string('button:addfaqtitle', 'block_faq_list'), 'POST', ['type' => 'primary']);

}
echo html_writer::end_div();
echo html_writer::end_div();

$tabs = $faqitem->helper->get_faq_list_items_language_tabs();
echo $OUTPUT->tabtree($tabs, 'faq_lang_' . $faqitem->helper->get_last_edit_faq_lang());

echo html_writer::table($table);

$actionurl = new moodle_url('/blocks/faq_list/view/faq_item_manage.php');
echo $OUTPUT->single_button($actionurl,
        get_string('button:addfaqitem', 'block_faq_list'), 'POST', ['type' => 'primary']);

$actionurl = new moodle_url('/blocks/faq_list/view/faq_lists.php');
echo $OUTPUT->single_button($actionurl,
        get_string('button:backtofaqlist', 'block_faq_list'), 'POST', ['type' => 'secondary']);

echo $OUTPUT->footer();
