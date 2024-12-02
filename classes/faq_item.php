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
 * FAQ list items (questions and answers) class.
 *
 * File         faq_item.php
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
use stdClass;

/**
 * FAQ item manager.
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class faq_item extends faq_list {

    /**
     * Get faq list item by id.
     *
     * @param int $id Id of selected faq list item.
     * @return stdClass|false Object of faq list item if exist otherwise false.
     * @throws dml_exception
     */
    public function get_by_id($id) {
        $conditions = [
                'id' => $id,
        ];
        if ($this->db->record_exists($this->tableitems, $conditions)) {
            return $this->db->get_record($this->tableitems, $conditions);
        }
        return false;
    }

    /**
     * Get all faq list items (questions and answers) for selected faq list and language.
     *
     * @param int $faqlistid Id of selected faq list.
     * @param string $lang Selected language, default is current language.
     * @return array Array of faq list items object.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function get_items($faqlistid, $lang = null) {
        $conditions = [
                'list_id' => $faqlistid,
                'lang' => $lang,
        ];
        return $this->db->get_records($this->tableitems, $conditions, 'sortorder');
    }

    /**
     * Get number of faq list items for selected faq list.
     *
     * @param int $faqlistid Id of selected faq list.
     * @param string $lang Selected language.
     * @return int Number of faq list items.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function count_items($faqlistid, $lang) {
        $conditions = [
                'list_id' => $faqlistid,
                'lang' => $lang,
        ];

        return $this->db->count_records($this->tableitems, $conditions);
    }

    /**
     * Create new faq list item.
     *
     * @param stdClass $data Faq list item object.
     * @return int|false Return id of successfully created faq list item otherwise false.
     * @throws dml_exception
     */
    public function create($data) {
        $faqlistid = $this->helper->get_last_edit_faq_list_id();
        $faqlang = $this->helper->get_last_edit_faq_lang();
        $itemcount = $this->count_items($faqlistid, $faqlang);

        $faqitemdata = new stdClass();
        $faqitemdata->list_id = $faqlistid;
        $faqitemdata->lang = $faqlang;
        $faqitemdata->question = $data->question;
        $faqitemdata->answer = $data->answer['text'];
        $faqitemdata->answer_format = $data->answer['format'];
        $faqitemdata->sortorder = $itemcount + 1;

        return $this->db->insert_record($this->tableitems, $faqitemdata, true);
    }

    /**
     * Update existing faq list item.
     *
     * @param stdClass $data Faq list item object.
     * @return bool True.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function update($data) {
        $faqitemdata = new stdClass();
        $faqitemdata->id = $data->id;
        $faqitemdata->question = $data->question;
        $faqitemdata->answer = $data->answer['text'];
        $faqitemdata->answer_format = $data->answer['format'];

        return $this->db->update_record($this->tableitems, $faqitemdata);
    }

    /**
     * Delete selecte faq list item.
     *
     * @param int $faqitemid Id of selected faq list item.
     * @return void
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function delete($faqitemid) {
        $faqitem = $this->get_by_id($faqitemid);
        $sortorder = $faqitem->sortorder;

        $this->db->delete_records($this->tableitems, ['id' => $faqitem->id]);
        $faqitems = $this->get_items($faqitem->list_id, $faqitem->lang);

        foreach ($faqitems as $faqitem) {
            if ($faqitem->sortorder > $sortorder) {
                $faqitem->sortorder = $faqitem->sortorder - 1;
                $this->db->update_record($this->tableitems, $faqitem);
            }
        }
    }

    /**
     * Move down faq list item (change sort-order of faq list items).
     *
     * @param int $faqitemid Id of selected faq list item.
     * @return void
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function movedown($faqitemid) {
        $faqitemselected = $this->get_by_id($faqitemid);
        $sortorder = $faqitemselected->sortorder;
        $faqitems = $this->get_items($faqitemselected->list_id, $faqitemselected->lang);

        foreach ($faqitems as $faqitem) {
            if ($faqitem->sortorder == $sortorder + 1) {
                $faqitem->sortorder = $sortorder;
                $this->db->update_record($this->tableitems, $faqitem);
            }
        }
        $faqitemselected->sortorder = $sortorder + 1;
        $this->db->update_record($this->tableitems, $faqitemselected);
    }

    /**
     * Move up faq list item (change sort-order of faq list items).
     *
     * @param int $faqitemid Id of selected faq list item.
     * @return void
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function moveup($faqitemid) {
        $faqitemselected = $this->get_by_id($faqitemid);
        $sortorder = $faqitemselected->sortorder;
        $faqitems = $this->get_items($faqitemselected->list_id, $faqitemselected->lang);

        foreach ($faqitems as $faqitem) {
            if ($faqitem->sortorder == $sortorder - 1) {
                $faqitem->sortorder = $sortorder;
                $this->db->update_record($this->tableitems, $faqitem);
            }
        }
        $faqitemselected->sortorder = $sortorder - 1;
        $this->db->update_record($this->tableitems, $faqitemselected);
    }
}
