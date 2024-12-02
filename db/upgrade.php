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
 * Upgrade definitions for the FAQ List module.
 *
 * File         upgrade.php
 * Encoding     UTF-8
 *
 * @package     block_faq_list
 *
 * @copyright   Agiledrop, 2024
 * @author      Agiledrop 2024 <hello@agiledrop.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Rename the database tables to add prefix.
 *
 * @param $oldversion
 * @return bool
 * @throws ddl_exception
 * @throws ddl_table_missing_exception
 */
function xmldb_block_faq_list_upgrade($oldversion): bool {
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2024112700) {
        $tablerenamings = [
            'faq_list' => 'block_faq_list_list',
            'faq_list_title' => 'block_faq_list_title',
            'faq_list_item' => 'block_faq_list_item',
        ];

        foreach ($tablerenamings as $oldname => $newname) {
            if ($dbman->table_exists($oldname) && !$dbman->table_exists($newname)) {
                $oldnametable = new xmldb_table($oldname);
                $dbman->rename_table($oldnametable, $newname);
                $DB->reset_caches();
            }
        }

        upgrade_block_savepoint(true, 2024112700, 'faq_list');
    }

    return true;
}
