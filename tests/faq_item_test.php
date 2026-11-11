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
 * File         faq_item_test.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Matej Pal <matej.pal@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_faq_list;

use advanced_testcase;
use stdClass;

/**
 * PHPUnit tests for faq item ordering and atomic list deletion.
 *
 * @package     block_faq_list
 * @group block_faq_list
 * @covers     \block_faq_list\faq_item
 * @covers     \block_faq_list\faq_list
 *
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class faq_item_test extends advanced_testcase {
    /** @var faq_item Item API. */
    private $items;

    /** @var faq_list List API. */
    private $lists;

    /** @var int Id of the list under test. */
    private $listid;

    /**
     * Setup: admin user, valid sesskey and one empty list.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $_POST['sesskey'] = sesskey();

        $this->items = new faq_item();
        $this->lists = new faq_list();

        $list = new stdClass();
        $list->shortname = 'testlist';
        $this->listid = $this->lists->create($list);

        $this->items->helper->set_last_edit_faq_list_id($this->listid);
        $this->items->helper->set_last_edit_faq_lang('en');
    }

    /**
     * Create an item through the public API.
     *
     * @param string $question Question text.
     * @return int Item id.
     */
    private function add(string $question): int {
        $data = new stdClass();
        $data->question = $question;
        $data->answer = ['text' => 'A', 'format' => FORMAT_HTML];
        $data->list_id = $this->listid;
        $data->faq_lang = $this->items->helper->get_last_edit_faq_lang();
        return $this->items->create($data);
    }

    /**
     * Questions in display order.
     *
     * @param string $lang Language.
     * @return string[]
     */
    private function questions(string $lang = 'en'): array {
        return array_values(array_map(fn($i) => $i->question, $this->items->get_items($this->listid, $lang)));
    }

    /**
     * New items are appended after the highest position, even when positions have gaps.
     */
    public function test_create_uses_max_not_count(): void {
        global $DB;
        $a = $this->add('A');
        $b = $this->add('B');
        $c = $this->add('C');
        // Create a gap: positions 1, 5, 9.
        $DB->set_field('block_faq_list_item', 'sortorder', 5, ['id' => $b]);
        $DB->set_field('block_faq_list_item', 'sortorder', 9, ['id' => $c]);

        $d = $this->add('D');
        $this->assertEquals(10, $DB->get_field('block_faq_list_item', 'sortorder', ['id' => $d]));
        $this->assertSame(['A', 'B', 'C', 'D'], $this->questions());
    }

    /**
     * Positions are counted per language.
     */
    public function test_create_position_is_per_language(): void {
        global $DB;
        $this->add('EN1');
        $this->add('EN2');
        $this->items->helper->set_last_edit_faq_lang('sl');
        $sl = $this->add('SL1');
        $this->assertEquals(1, $DB->get_field('block_faq_list_item', 'sortorder', ['id' => $sl]));
    }

    /**
     * Items with the same position are still returned in a stable order (by id).
     */
    public function test_get_items_is_deterministic(): void {
        global $DB;
        $a = $this->add('A');
        $b = $this->add('B');
        $c = $this->add('C');
        $DB->set_field('block_faq_list_item', 'sortorder', 1, ['id' => $b]);
        $DB->set_field('block_faq_list_item', 'sortorder', 1, ['id' => $c]);

        for ($i = 0; $i < 5; $i++) {
            $this->assertSame([$a, $b, $c], array_values(array_map(
                fn($x) => (int)$x->id,
                $this->items->get_items($this->listid, 'en')
            )));
        }
    }

    /**
     * The first item cannot move up and the last cannot move down.
     */
    public function test_move_boundaries(): void {
        global $DB;
        $a = $this->add('A');
        $b = $this->add('B');

        $this->assertFalse($this->items->moveup($a));
        $this->assertFalse($this->items->movedown($b));
        $this->assertSame(['A', 'B'], $this->questions());
        $this->assertEquals(1, $DB->get_field('block_faq_list_item', 'sortorder', ['id' => $a]));
        $this->assertEquals(2, $DB->get_field('block_faq_list_item', 'sortorder', ['id' => $b]));
    }

    /**
     * Moving swaps with the neighbour and works across gaps.
     */
    public function test_move_swaps_neighbours_across_gaps(): void {
        global $DB;
        $a = $this->add('A');
        $b = $this->add('B');
        $c = $this->add('C');
        $DB->set_field('block_faq_list_item', 'sortorder', 10, ['id' => $c]);

        $this->assertTrue($this->items->moveup($c));
        $this->assertSame(['A', 'C', 'B'], $this->questions());
        $this->assertTrue($this->items->movedown($a));
        $this->assertSame(['C', 'A', 'B'], $this->questions());
    }

    /**
     * A move never touches items of another language.
     */
    public function test_move_ignores_other_language(): void {
        $this->add('EN1');
        $this->items->helper->set_last_edit_faq_lang('sl');
        $sl = $this->add('SL1');
        $this->assertFalse($this->items->moveup($sl));
        $this->assertFalse($this->items->movedown($sl));
    }

    /**
     * Deleting an item closes the gap and leaves other lists and languages alone.
     */
    public function test_delete_renumbers_remaining(): void {
        global $DB;
        $a = $this->add('A');
        $b = $this->add('B');
        $c = $this->add('C');
        $this->items->helper->set_last_edit_faq_lang('sl');
        $sl = $this->add('SL1');

        $this->items->delete($b);

        $this->assertSame(['A', 'C'], $this->questions());
        $this->assertEquals(1, $DB->get_field('block_faq_list_item', 'sortorder', ['id' => $a]));
        $this->assertEquals(2, $DB->get_field('block_faq_list_item', 'sortorder', ['id' => $c]));
        $this->assertEquals(1, $DB->get_field('block_faq_list_item', 'sortorder', ['id' => $sl]));
    }

    /**
     * Deleting a list removes its row, titles and items.
     */
    public function test_delete_list_removes_everything(): void {
        global $DB;
        $this->add('A');
        $this->lists->add_title_translation($this->listid, 'Title', 'en');

        $this->lists->delete($this->listid);

        $this->assertFalse($DB->record_exists('block_faq_list_list', ['id' => $this->listid]));
        $this->assertFalse($DB->record_exists('block_faq_list_title', ['list_id' => $this->listid]));
        $this->assertFalse($DB->record_exists('block_faq_list_item', ['list_id' => $this->listid]));
    }

    /**
     * A failure inside the locked transaction rolls everything back and releases the lock.
     */
    public function test_failed_operation_rolls_back_and_releases_lock(): void {
        global $DB;
        $a = $this->add('A');

        try {
            helper::run_locked_transaction($this->listid, function () use ($DB, $a) {
                $DB->delete_records('block_faq_list_item', ['id' => $a]);
                throw new \moodle_exception('error:required', 'block_faq_list');
            });
            $this->fail('Exception expected.');
        } catch (\moodle_exception $e) {
            $this->assertTrue($DB->record_exists('block_faq_list_item', ['id' => $a]));
        }

        // The lock was released, so the same list can be locked again straight away.
        $this->assertSame('ok', helper::run_locked_transaction($this->listid, fn() => 'ok'));
    }

    /**
     * Only one title per list and language can be created.
     */
    public function test_duplicate_title_translation_is_rejected(): void {
        global $DB;
        $this->lists->add_title_translation($this->listid, 'First', 'en');

        try {
            $this->lists->add_title_translation($this->listid, 'Second', 'en');
            $this->fail('Exception expected.');
        } catch (\moodle_exception $e) {
            $this->assertSame('error:titleexists', $e->errorcode);
        }
        $this->assertEquals(1, $DB->count_records('block_faq_list_title', ['list_id' => $this->listid]));

        // Another language is still allowed.
        $this->assertNotEmpty($this->lists->add_title_translation($this->listid, 'Drugi', 'sl'));
    }

    /**
     * The database itself refuses a duplicate list and language pair.
     */
    public function test_unique_index_on_titles(): void {
        global $DB;
        $row = (object)['list_id' => $this->listid, 'lang' => 'en', 'title' => 'One'];
        $DB->insert_record('block_faq_list_title', $row);
        $this->expectException(\dml_exception::class);
        $DB->insert_record('block_faq_list_title', $row);
    }

    /**
     * An explicit language is used instead of the session one, and a missing list is refused.
     */
    public function test_add_title_uses_given_language_and_checks_list(): void {
        global $DB;
        $this->items->helper->set_last_edit_faq_lang('en');
        $id = $this->lists->add_title_translation($this->listid, 'Naslov', 'sl');
        $this->assertSame('sl', $DB->get_field('block_faq_list_title', 'lang', ['id' => $id]));

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('msgfaqlistnotexist', 'block_faq_list'));
        $this->lists->add_title_translation($this->listid + 1000, 'Orphan', 'en');
    }

    /**
     * Creating a question for a missing list is refused and nothing is stored.
     */
    public function test_create_item_for_missing_list_is_refused(): void {
        global $DB;
        $data = (object)[
            'question' => 'Orphan',
            'answer' => ['text' => 'A', 'format' => FORMAT_HTML],
            'list_id' => $this->listid + 1000,
            'faq_lang' => 'en',
        ];
        try {
            $this->items->create($data);
            $this->fail('Exception expected.');
        } catch (\moodle_exception $e) {
            $this->assertSame('msgfaqlistnotexist', $e->errorcode);
        }
        $this->assertSame(0, $DB->count_records('block_faq_list_item', ['list_id' => $this->listid + 1000]));
    }

    /**
     * Updating a missing item or title is refused instead of silently doing nothing.
     */
    public function test_update_missing_records_is_refused(): void {
        $data = (object)['id' => 99999, 'question' => 'Q', 'answer' => ['text' => 'A', 'format' => FORMAT_HTML]];
        try {
            $this->items->update($data);
            $this->fail('Exception expected.');
        } catch (\moodle_exception $e) {
            $this->assertSame('msgfaqitemnotexist', $e->errorcode);
        }

        $this->expectException(\moodle_exception::class);
        $this->lists->update_title_translation(99999, 'Title');
    }
}
