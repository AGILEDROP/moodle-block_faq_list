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
 * General global plugin settings
 *
 * File         settings.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('moodle_internal not defined');

require_once($CFG->libdir . '/adminlib.php');

global $ADMIN;

if ($hassiteconfig) {

    $ADMIN->add('root', new admin_category('block_faq_list', get_string('admin:faqcategorytitle', 'block_faq_list')));

    $ADMIN->add('block_faq_list',
        new admin_externalpage('faq_list',
        get_string('admin:faqmanagelist', 'block_faq_list'),
        new moodle_url('/blocks/faq_list/view/faq_lists.php')));

    $ADMIN->add('block_faq_list',
        new admin_externalpage('faq_list_items',
            get_string('admin:faqmanageitem', 'block_faq_list'),
            new moodle_url('/blocks/faq_list/view/faq_list_items.php')));
}
