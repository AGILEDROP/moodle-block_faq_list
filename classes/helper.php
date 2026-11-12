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
 * FAQ list helper class.
 *
 * File         helper.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_faq_list;

use cache;
use coding_exception;
use moodle_exception;

/**
 * Helper class.
 *
 * @package     block_faq_list
 *
 * @property-read cache $cache
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /** @var cache Instance of cache. */
    private $cache;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->cache = cache::make('block_faq_list', 'last_edit');
    }

    /**
     * Get last edited faq list id from session cache.
     *
     * @return int|false Id or false.
     * @throws coding_exception
     */
    public function get_last_edit_faq_list_id() {
        return $this->cache->get('faq_list_id');
    }

    /**
     * Set last edited faq list id to session cache.
     *
     * @param int $faqlistid Id of faq list.
     * @return bool
     */
    public function set_last_edit_faq_list_id($faqlistid) {
        return $this->cache->set('faq_list_id', (int)$faqlistid);
    }

    /**
     * Get last edited faq list item id from session cache.
     *
     * @return int|false Id or false.
     * @throws coding_exception
     */
    public function get_last_edit_faq_item_id() {
        return $this->cache->get('faq_item_id');
    }

    /**
     * Set last edited faq list item it to session cache.
     *
     * @param int $faqitemid Id of faq list item.
     * @return bool
     */
    public function set_last_edit_faq_item_id($faqitemid) {
        return $this->cache->set('faq_item_id', (int)$faqitemid);
    }

    /**
     * Get last edited faq list language from session cache.
     *
     * @return string|false Lang-code of faq list or false.
     * @throws coding_exception
     */
    public function get_last_edit_faq_lang() {
        if ($this->cache->get('faq_lang')) {
            return $this->cache->get('faq_lang');
        }

        $currentlanguage = current_language();
        $this->set_last_edit_faq_lang($currentlanguage);
        return $currentlanguage;
    }

    /**
     * Set last edited faq list language to session cache.
     *
     * @param string $langcode Last edited faq list lang-code.
     * @return bool
     */
    public function set_last_edit_faq_lang($langcode) {
        return $this->cache->set('faq_lang', $langcode);
    }

    /**
     * Get array of tabs. All installed language packs are included.
     *
     * @return array Array of language tabs.
     * @throws moodle_exception
     */
    public function get_faq_list_items_language_tabs() {

        $tabs = [];
        $translations = get_string_manager()->get_list_of_translations();

        foreach ($translations as $translationid => $translation) {
            $url = new \moodle_url('/blocks/faq_list/view/faq_list_items.php', [
                'faq_lang' => $translationid,
            ]);

            $tabs[] = new \tabobject(
                'faq_lang_' . $translationid,
                $url,
                strtoupper($translationid),
                $translation,
                false,
            );
        }
        return $tabs;
    }

    /**
     * Run a callback while holding an exclusive lock for a faq list, inside a database transaction.
     *
     * The lock serialises concurrent ordering changes (create, move, delete) on the same faq list across
     * requests and cluster nodes. A transaction alone cannot do that, because two transactions can both read
     * the same positions before either writes. The lock is taken before the transaction starts, so the
     * callback always sees data committed by the previous lock holder, and it is released after commit or
     * rollback.
     *
     * @param int $faqlistid Id of the faq list to lock.
     * @param callable $callback Work to do while holding the lock.
     * @return mixed Whatever the callback returns.
     * @throws moodle_exception If the lock cannot be acquired.
     * @throws \Throwable Anything thrown by the callback, after the transaction is rolled back.
     */
    public static function run_locked_transaction(int $faqlistid, callable $callback) {
        global $DB;

        $lockfactory = \core\lock\lock_config::get_lock_factory('block_faq_list');
        $lock = $lockfactory->get_lock('list_' . $faqlistid, 10);
        if (!$lock) {
            throw new moodle_exception('error:locktimeout', 'block_faq_list');
        }

        try {
            $transaction = $DB->start_delegated_transaction();
            try {
                $result = $callback();
            } catch (\Throwable $e) {
                $transaction->rollback($e instanceof \Exception ? $e : new \Exception($e->getMessage(), 0, $e));
            }
            $transaction->allow_commit();
            return $result;
        } finally {
            $lock->release();
        }
    }
}
