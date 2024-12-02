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
 * FAQ list class.
 *
 * File         faq_list.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_faq_list;

use dml_exception;
use moodle_database;
use stdClass;

/**
 * FAQ list manager.
 *
 * @package     block_faq_list
 *
 * @property-read moodle_database $db
 * @property-read helper $helper
 * @property-read string $tablelists
 * @property-read string $tabletitles
 * @property-read string $tableitems
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class faq_list {

    /** @var moodle_database Database connection. */
    public $db;

    /** @var helper Helper class instance. */
    public $helper;

    /** @var string Database table name, which store faq lists. */
    public $tablelists;

    /** @var string Database table name, which store faq list title translations. */
    public $tabletitles;

    /** @var string Database table name, which store multi-language faq list items (questions and answers). */
    public $tableitems;

    /**
     * Constructor.
     */
    public function __construct() {
        global $DB;
        $this->db = $DB;
        $this->helper = new helper();
        $this->tablelists = 'block_faq_list_list';
        $this->tabletitles = 'block_faq_list_title';
        $this->tableitems = 'block_faq_list_item';
    }

    /**
     * Check if faq list with given shortname exist.
     *
     * @param string $shortname Id of faq list.
     * @return bool True if exist otherwise false.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function exist($shortname) {
        $conditions = [
                'shortname' => $shortname,
        ];

        return $this->db->record_exists($this->tablelists, $conditions);
    }

    /**
     * Get faq list object with given shortname.
     *
     * @param string $shortname Shortname of faq list.
     * @return stdClass|false Faq list object otherwise false.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_by_shortname($shortname) {
        $conditions = [
                'shortname' => $shortname,
        ];
        if ($this->db->record_exists($this->tablelists, $conditions)) {
            return $this->db->get_record($this->tablelists, $conditions);
        }
        return false;
    }

    /**
     * Get faq list object with given id.
     *
     * @param int $id Id of faq list.
     * @return stdClass|bool Faq list object otherwise false.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_by_id($id) {
        $conditions = [
                'id' => $id,
        ];
        if ($this->db->record_exists($this->tablelists, $conditions)) {
            return $this->db->get_record($this->tablelists, $conditions);
        }
        return false;
    }

    /**
     * Get all faq lists.
     *
     * @return array Array of all faq lists.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_all() {
        return $this->db->get_records($this->tablelists, []);
    }

    /**
     * Get all faq list items (questions and answers) for selected faq list id.
     *
     * @param int $faqlistid Id of faq list.
     * @param string $lang Selected language.
     * @return array Array of faq items.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_items($faqlistid, $lang = null) {
        if (!$lang) {
            $lang = current_language();
        }

        $conditions = [
                'list_id' => $faqlistid,
                'lang' => $lang,
        ];

        return $this->db->get_records($this->tableitems, $conditions, 'sortorder');
    }

    /**
     * Create new faq list.
     *
     * @param stdClass $faqlist Faq list object.
     * @return int|false Return id of successfully created faq list, otherwise false.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function create($faqlist) {
        $data = new stdClass();
        $data->shortname = $faqlist->shortname;

        return $this->db->insert_record($this->tablelists, $data, true);
    }

    /**
     * Update existing faq list.
     *
     * @param stdClass $faqlist Faq list object.
     * @return bool True.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function update($faqlist) {
        $data = new stdClass();
        $data->id = $faqlist->id;
        $data->shortname = $faqlist->shortname;

        return $this->db->update_record($this->tablelists, $data);
    }

    /**
     * Delete existing faq list with selected $id and all items of it.
     *
     * @param int $id Id of faq list.
     * @return void
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function delete($id) {
        $this->db->delete_records($this->tablelists, ['id' => $id]);
        $this->db->delete_records($this->tabletitles, ['list_id' => $id]);
        $this->db->delete_records($this->tableitems, ['list_id' => $id]);
    }

    /**
     * Get available faq lists array.
     *
     * @return array Array of all available faq lists with structure ['id'] => shortname.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_available_faq_list_dropdown_options() {
        $data = [];
        $faqlists = $this->get_all();

        foreach ($faqlists as $faqlist) {
            $data[$faqlist->id] = $faqlist->shortname;
        }

        return $data;
    }

    /**
     * Get translated title for selected faq list and language.
     *
     * @param int $faqlistid Id of faq list.
     * @param string $lang Selected language (default is current language).
     * @return stdClass|false Object of translated title otherwise false.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_title($faqlistid, $lang = null) {
        if (!$lang) {
            $lang = current_language();
        }
        $conditions = [
                'list_id' => $faqlistid,
                'lang' => $lang,
        ];
        if ($this->db->record_exists($this->tabletitles, $conditions)) {
            return $this->db->get_record($this->tabletitles, $conditions);
        }

        return false;
    }

    /**
     * Get faq list title by own id.
     *
     * @param string $faqtitleid Id of selected title.
     * @return stdClass|false Object of selected title if exist otherwise false.
     * @throws dml_exception
     */
    public function get_title_by_id($faqtitleid) {
        $conditions = [
                'id' => $faqtitleid,
        ];
        if ($this->db->record_exists($this->tabletitles, $conditions)) {
            return $this->db->get_record($this->tabletitles, $conditions);
        }
        return false;
    }

    /**
     * Add faq list title translation for selected faq list.
     *
     * @param int $faqlistid Id of faq list.
     * @param string $title Title translation.
     * @return bool|int Return id of title translation otherwise false.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function add_title_translation($faqlistid, $title) {
        $data = new stdClass();
        $data->list_id = $faqlistid;
        $data->lang = $this->helper->get_last_edit_faq_lang();
        $data->title = $title;

        return $this->db->insert_record($this->tabletitles, $data, true);
    }

    /**
     * Update selected title translation.
     *
     * @param int $faqtitleid Id of existing faq title translation.
     * @param string $title Title translation.
     * @return bool True.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function update_title_translation($faqtitleid, $title) {
        $data = new stdClass();
        $data->id = $faqtitleid;
        $data->title = $title;

        return $this->db->update_record($this->tabletitles, $data);
    }

    /**
     * Export values for template for selected faq list.
     *
     * @param string $shortname Shortname of selected faq list.
     * @param bool $showtitle Display faq list title or not.
     * @return array
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function export_faq_list($shortname, $showtitle = false) {
        $data = [];

        if (!$this->exist($shortname)) {
            return $data;
        }

        $existingfaqlist = $this->get_by_shortname($shortname);
        $faqlisttitle = $this->get_title($existingfaqlist->id);
        $faqlistitems = $this->get_items($existingfaqlist->id);

        $data['show_title'] = $showtitle;

        if ($showtitle && $faqlisttitle) {
            $data['faq_title'] = $faqlisttitle->title;
        } else {
            $data['faq_title'] = '';
        }

        $itemnumber = 0;
        foreach ($faqlistitems as $faqlistitem) {
            $item = [
                'itemnumber' => $itemnumber,
                'id' => $faqlistitem->id,
                'question' => $faqlistitem->question,
                'answer' => $faqlistitem->answer,
            ];
            $data['faq_items'][] = $item;
            $itemnumber++;
        }
        return $data;
    }

    /**
     * Export values for template for selected faq list.
     *
     * @param int $faqlistid Id of selected faq list.
     * @param bool $showtitle Display faq list title or not.
     * @return array
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function export_faq_list_by_id($faqlistid, $showtitle = false) {
        $faqlist = $this->get_by_id($faqlistid);

        if ($faqlist) {
            return $this->export_faq_list($faqlist->shortname, $showtitle);
        }

        return [];
    }
}
