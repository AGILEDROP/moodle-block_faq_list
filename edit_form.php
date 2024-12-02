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
 * Edit form for each block instance.
 *
 * File         block_faq_list_edit_form.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_faq_list\faq_list;

/**
 * Block instance configuration form.
 *
 * @package     block_faq_list
 *
 * @property-read block_base $block
 * @property-read moodle_page $page
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_faq_list_edit_form extends block_edit_form {

    /**
     * Form definitions for each block instance
     * @param MoodleQuickForm $mform
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function specific_definition($mform) {

        $hasconfig = $this->block->config;

        $faqlist = new faq_list();

        // Selected faq list.
        $availablefaqlists = $faqlist->get_available_faq_list_dropdown_options();
        $mform->addElement('select',
                'config_faq_list_id',
                get_string('label:configfaqlistid', 'block_faq_list'),
                $availablefaqlists,
        );
        $mform->addHelpButton('config_faq_list_id', 'label:configfaqlistid', 'block_faq_list');
        $mform->setType('config_faq_list_id', PARAM_ALPHANUM);
        $mform->addRule('config_faq_list_id', get_string('error:required', 'block_faq_list'), 'required', null, 'client');
        if (isset($this->block->config->faq_list_id)) {
            $mform->setDefault('config_faq_list_id', $this->block->config->faq_list_id);
        }

        // Block title display.
        $availableblocktitles = [
                'none' => get_string('label:configblocktitlenone', 'block_faq_list'),
                'pluginname' => get_string('label:configblocktitlepluginname', 'block_faq_list'),
                'faqlisttitle' => get_string('label:configblocktitlefaq', 'block_faq_list'),
        ];
        $mform->addElement('select',
                'config_block_title',
                get_string('label:configblocktitle', 'block_faq_list'),
                $availableblocktitles,
        );
        $mform->addHelpButton('config_block_title', 'label:configblocktitle', 'block_faq_list');
        $mform->setType('config_block_title', PARAM_ALPHANUM);
        $mform->addRule('config_block_title', get_string('error:required', 'block_faq_list'), 'required', null, 'client');
        if (isset($this->block->config->block_title)) {
            $mform->setDefault('config_block_title', $this->block->config->block_title);
        } else {
            $mform->setDefault('config_block_title', 'faqlisttitle');
        }

        // FAQ list title.
        $mform->addElement('selectyesno', 'config_show_faq_title', get_string('label:configshowfaqtitle', 'block_faq_list'));
        $mform->addHelpButton('config_show_faq_title', 'label:configshowfaqtitle', 'block_faq_list');
        $mform->setType('config_show_faq_title', PARAM_ALPHANUM);
        $mform->addRule('config_show_faq_title', get_string('error:required', 'block_faq_list'), 'required', null, 'client');
        if (isset($this->block->config->show_faq_title)) {
            $mform->setDefault('config_show_faq_title', $this->block->config->show_faq_title);
        } else {
            $mform->setDefault('config_show_faq_title', 'no');
        }

        // Display FAQ list as.
        $displayoptions = [
            'default' => 'Default',
            'accordion' => 'Accordion',
            'carousel' => 'Carousel',
        ];
        $mform->addElement('select',
                'config_display_type',
                get_string('label:configdisplaytype', 'block_faq_list'),
                $displayoptions
        );
        $mform->setType('config_display_type', PARAM_ALPHANUMEXT);
        $mform->addRule('config_display_type', get_string('error:required', 'block_faq_list'), 'required', null, 'client');
        if (isset($this->block->config->display_type)) {
            $mform->setDefault('config_display_type', $this->block->config->display_type);
        } else {
            $mform->setDefault('config_display_type', 'default');
        }
    }

    /**
     * Display the configuration form when block is being added to the page
     *
     * @return bool
     */
    public static function display_form_when_adding(): bool {
        return true;
    }
}
