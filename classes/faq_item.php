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
        require_sesskey();

        $context = \context_system::instance();
        require_capability('block/faq_list:createormodify', $context);

        $faqlistid = $this->helper->get_last_edit_faq_list_id();
        $faqlang = $this->helper->get_last_edit_faq_lang();

        return helper::run_locked_transaction($faqlistid, function () use ($faqlistid, $faqlang, $data) {
            // Next position is calculated under the lock, so concurrent creates cannot get the same one.
            $maxsortorder = (int)$this->db->get_field_select(
                $this->tableitems,
                'MAX(sortorder)',
                'list_id = :listid AND lang = :lang',
                ['listid' => $faqlistid, 'lang' => $faqlang]
            );

            $faqitemdata = new stdClass();
            $faqitemdata->list_id = $faqlistid;
            $faqitemdata->lang = $faqlang;
            $faqitemdata->question = $data->question;
            $faqitemdata->answer = $data->answer['text'];
            $faqitemdata->answer_format = $data->answer['format'];
            $faqitemdata->sortorder = $maxsortorder + 1;

            return $this->db->insert_record($this->tableitems, $faqitemdata, true);
        });
    }

    /**
     * Update existing faq list item.
     *
     * @param stdClass $data Faq list item object.
     * @return bool True.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function update($data) {
        require_sesskey();

        $context = \context_system::instance();
        require_capability('block/faq_list:createormodify', $context);

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
        require_sesskey();

        $context = \context_system::instance();
        require_capability('block/faq_list:delete', $context);

        $faqitem = $this->get_by_id($faqitemid);
        if (!$faqitem) {
            return;
        }

        helper::run_locked_transaction($faqitem->list_id, function () use ($faqitemid) {
            // Re-read under the lock: the position may have changed since the unlocked read.
            $faqitem = $this->get_by_id($faqitemid);
            if (!$faqitem) {
                return;
            }

            $this->db->delete_records($this->tableitems, ['id' => $faqitem->id]);

            // Close the gap with a single statement instead of updating each row separately.
            $this->db->execute(
                "UPDATE {{$this->tableitems}}
                    SET sortorder = sortorder - 1
                  WHERE list_id = :listid
                    AND lang = :lang
                    AND sortorder > :sortorder",
                [
                    'listid' => $faqitem->list_id,
                    'lang' => $faqitem->lang,
                    'sortorder' => $faqitem->sortorder,
                ]
            );
        });
    }

    /**
     * Move down faq list item (change sort-order of faq list items).
     *
     * Does nothing if the item is already last in its list and language.
     *
     * @param int $faqitemid Id of selected faq list item.
     * @return bool True if the item was moved, false otherwise.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function movedown($faqitemid) {
        return $this->move($faqitemid, true);
    }

    /**
     * Move up faq list item (change sort-order of faq list items).
     *
     * Does nothing if the item is already first in its list and language.
     *
     * @param int $faqitemid Id of selected faq list item.
     * @return bool True if the item was moved, false otherwise.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function moveup($faqitemid) {
        return $this->move($faqitemid, false);
    }

    /**
     * Swap sort-order with the adjacent item in the same list and language.
     *
     * @param int $faqitemid Id of selected faq list item.
     * @param bool $down True to move down, false to move up.
     * @return bool True if the item was moved, false if no adjacent item exists.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    private function move($faqitemid, $down) {
        require_sesskey();

        $context = \context_system::instance();
        require_capability('block/faq_list:createormodify', $context);

        $selected = $this->get_by_id($faqitemid);
        if (!$selected) {
            return false;
        }

        return helper::run_locked_transaction($selected->list_id, function () use ($faqitemid, $down) {
            // Re-read under the lock so the neighbour is found from current positions.
            $selected = $this->get_by_id($faqitemid);
            if (!$selected) {
                return false;
            }

            $operator = $down ? '>' : '<';
            $direction = $down ? 'ASC' : 'DESC';
            $sql = "SELECT *
                      FROM {{$this->tableitems}}
                     WHERE list_id = :listid
                       AND lang = :lang
                       AND sortorder $operator :sortorder
                  ORDER BY sortorder $direction, id $direction";
            $params = [
                'listid' => $selected->list_id,
                'lang' => $selected->lang,
                'sortorder' => $selected->sortorder,
            ];
            $adjacent = $this->db->get_records_sql($sql, $params, 0, 1);
            if (!$adjacent) {
                return false;
            }
            $adjacent = reset($adjacent);

            $selectedsortorder = $selected->sortorder;
            $selected->sortorder = $adjacent->sortorder;
            $adjacent->sortorder = $selectedsortorder;
            $this->db->update_record($this->tableitems, $selected);
            $this->db->update_record($this->tableitems, $adjacent);

            return true;
        });
    }
}
