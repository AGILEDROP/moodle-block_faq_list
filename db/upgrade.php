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
 * @param int $oldversion
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

    if ($oldversion < 2025011401) {
        $table = new xmldb_table('block_faq_list_title');
        $index = new xmldb_index('list_lang', XMLDB_INDEX_UNIQUE, ['list_id', 'lang']);

        if (!$dbman->index_exists($table, $index)) {
            // Keep the lowest-id translation for each list and language.
            // Delete the other translations, even if their title text differs, before adding the unique index.
            $duplicates = $DB->get_records_sql(
                "SELECT MIN(id) AS keepid, list_id, lang
                   FROM {block_faq_list_title}
               GROUP BY list_id, lang
                 HAVING COUNT(*) > 1"
            );

            $transaction = $DB->start_delegated_transaction();
            foreach ($duplicates as $duplicate) {
                $DB->delete_records_select(
                    'block_faq_list_title',
                    'list_id = :listid AND lang = :lang AND id <> :keepid',
                    [
                        'listid' => $duplicate->list_id,
                        'lang' => $duplicate->lang,
                        'keepid' => $duplicate->keepid,
                    ]
                );
            }
            $transaction->allow_commit();

            $dbman->add_index($table, $index);
        }

        upgrade_block_savepoint(true, 2025011401, 'faq_list');
    }

    return true;
}
