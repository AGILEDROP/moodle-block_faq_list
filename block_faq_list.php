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
 * Block implementation
 *
 * File         block_faq_list.php
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
 * block_faq_list
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_faq_list extends block_base {

    /**
     * initializes block
     */
    public function init() {
        global $CFG;
        $this->title = get_string('blockname', 'block_faq_list');
        include($CFG->dirroot . '/blocks/faq_list/version.php');
        $this->version = $plugin->version;
    }

    /**
     * Get/load block contents
     * @return stdClass
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $faqlist = new faq_list();
        $faqlistid = $this->config->faq_list_id;
        $titleshow = (bool)$this->config->show_title['show_title'];
        $displaytype = $this->config->display_type;

        $templatecontext = $faqlist->export_faq_list_by_id($faqlistid, $titleshow);

        $renderer = $this->page->get_renderer('core');

        switch ($displaytype) {
            case 'type_2':
                // Select template.
                $templatename = 'block_faq_list/faq_list';
                break;
            case 'type_1':
                // Select other template.
                $templatename = 'block_faq_list/faq_list';
                break;
            default:
                // Select default template.
                $templatename = 'block_faq_list/faq_list';
                break;

        }
        $content = $renderer->render_from_template($templatename, $templatecontext);

        $this->content = new stdClass();

        $this->content->text = $content;

        return $this->content;
    }

    /**
     * Which page types this block may appear on.
     *
     * @return array page-type prefix => true/false.
     */
    public function applicable_formats() {
        return ['all' => true];
    }

    /**
     * Is each block of this type going to have instance-specific configuration?
     *
     * @return bool true
     */
    public function instance_allow_config() {
        return true;
    }

    /**
     * Allow multiple instances of this block?
     *
     * @return bool false
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Do we hide the block header?
     *
     * @return bool false
     */
    public function hide_header() {
        return false;
    }

    /**
     * has own config?
     *
     * @return bool true
     */
    public function has_config() {
        return true;
    }

}
