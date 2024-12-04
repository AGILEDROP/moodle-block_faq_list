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
 * File         helper_test.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_faq_list\tests;

use block_faq_list\helper;
use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * PHPUnit tests for the block_faq_list helper class.
 *
 * @package     block_faq_list
 *
 * @group block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper_test extends advanced_testcase
{

    /** @var helper Instance of helper class. */
    private $helper;

    /**
     * Setup before tests.
     */
    protected function setUp(): void
    {
        $this->resetAfterTest(true);
        $this->helper = new helper();
    }

    /**
     * Test setting and getting last edited FAQ list ID.
     */
    public function test_set_and_get_last_edit_faq_list_id()
    {
        $faqListId = 123;
        $this->assertTrue($this->helper->set_last_edit_faq_list_id($faqListId));
        $this->assertEquals($faqListId, $this->helper->get_last_edit_faq_list_id());
    }

    /**
     * Test setting and getting last edited FAQ item ID.
     */
    public function test_set_and_get_last_edit_faq_item_id()
    {
        $faqItemId = 456;
        $this->assertTrue($this->helper->set_last_edit_faq_item_id($faqItemId));
        $this->assertEquals($faqItemId, $this->helper->get_last_edit_faq_item_id());
    }

    /**
     * Test setting and getting last edited FAQ language.
     */
    public function test_set_and_get_last_edit_faq_lang()
    {
        $faqLang = 'en';
        $this->assertTrue($this->helper->set_last_edit_faq_lang($faqLang));
        $this->assertEquals($faqLang, $this->helper->get_last_edit_faq_lang());
    }

    /**
     * Test getting FAQ list items language tabs.
     */
    public function test_get_faq_list_items_language_tabs()
    {
        $tabs = $this->helper->get_faq_list_items_language_tabs();

        $this->assertIsArray($tabs);

        if (!empty($tabs)) {
            $firstTab = $tabs[0];
            $this->assertInstanceOf(\tabobject::class, $firstTab);
            $this->assertNotEmpty($firstTab->id);
            $this->assertNotEmpty($firstTab->link);
            $this->assertNotEmpty($firstTab->text);
        }
    }
}