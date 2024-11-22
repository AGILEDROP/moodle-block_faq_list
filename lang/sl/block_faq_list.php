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
 * Language file for block_faq_list, EN
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

// Capabilities.


// DEFAULT.
$string['blockname'] = 'Seznam pogostih vprašanj';
$string['pluginname'] = 'Seznam pogostih vprašanj';

// Headers.
$string['header:faq_list'] = 'Seznam pogostih vprašanj';
$string['header:faq_list_add'] = 'Ustvari nov seznam pogostih vprašanj';
$string['header:faq_list_edit'] = 'Upravljaj s seznamom pogostih vprašanj';
$string['header:faq_list_delete'] = 'Izbriši seznam pogostih vprašanj';
$string['header:faq_item_list'] = 'Seznam vprašanj in odgovorov';
$string['header:faq_item_add'] = 'Ustvari nov seznam pogostih vprašanj';
$string['header:faq_item_edit'] = 'Urejaj vprašanje';
$string['header:faq_item_delete'] = 'Izbriši vprašanje';
$string['header:faq_title_management'] = 'Prevod naslova seznama pogostih vprašanj';
$string['header:faq_title'] = 'Naslov';

// Success strings.


// URL texts.
$string['admin:faq_category_title'] = 'Seznam seznamov pogostih vprašanj';
$string['admin:faq_manage_list'] = 'Upravljanje seznama pogostih vprašanj';
$string['admin:faq_manage_item'] = 'Upravljanje';
$string['tab_faq_list'] = 'Seznam seznamov pogostih vprašanj';
$string['tab_faq_list_title'] = 'Seznam vseh seznamov pogostih vprašanj';
$string['tab_faq_list_add'] = 'Ustvari nov seznam pogostih vprašanj';
$string['tab_faq_list_add_title'] = 'Ustvari nov seznam pogostih vprašanj ali urejaj Create new FAQ list or edit existing one';


// Form Labels.
$string['label:config_faq_list_id'] = 'Seznam pogostih vprašanj';
$string['label:config_faq_list_id_help'] = 'Izberi kateri seznam pogostih vprašanj bo prikazan.';

$string['label:config_block_title'] = 'Prikaži naslov bloka';
$string['label:config_block_title_help'] = 'Select how block title is displayed. If you choose display as faq title, note that this title must be provided for current language.';
$string['label:config_block_title_none'] = 'Nič - skrij naslov bloka';
$string['label:config_block_title_pluginname'] = 'Prikaži naslov bloka kot ime vtičnika';
$string['label:config_block_title_faq'] = 'Prikaži ime bloka kot naslov izbranega seznama pogostih vprašanj.';

$string['label:config_show_faq_title'] = 'Prikaži naslov seznama pogostih vprašanj';
$string['label:config_show_faq_title_help'] = 'Prikaži naslov seznama pogostih vprašanj ali ne. Če ne pazimo, lahko prikažemo enak naslov dvakrat.';

$string['label:config_display_type'] = 'Prikaži seznam pogostih vprašanj kot';
$string['label:faq_list_shortname'] = 'Unikatno kratko ime';
$string['label:faq_list_shortname_help'] = 'Vnesi unikatno ime. Dovoljeni znaki so: [0-9][A-Z][a-z][_]';
$string['label:faq_list_description'] = 'Opis';
$string['label:faq_list_description_help'] = 'Kratek opis';
$string['label:faq_list_delete_question'] = 'Ali želite izbrisati seznam pogostih vprašanj?';
$string['label:faq_item_delete_question'] = 'Ali želite izbrisati element seznama pogostih vprašanj?';
$string['label:faq_question'] = 'Vprašanje';
$string['label:faq_question_help'] = 'Napišite besedilo za vprašanje.';
$string['label:faq_answer'] = 'Odgovor';
$string['label:faq_answer_help'] = 'Napišite besedilo za odgovor.';
$string['label:faq_title'] = 'Prevod naslova seznama pogostih vprašanj';
$string['label:faq_title_help'] = 'Vnesite preveden naslov za izbran seznam pogostih vprašanj.';

// Errors.
$string['error:required'] = 'To polje je obvezno.';
$string['error:unique'] = 'To kratko ime že obstaja.';

// Buttons.
$string['button:delete_faq_list'] = 'Izbriši';
$string['button:edit_faq_list'] = 'Uredi';
$string['button:back_to_faq_list'] = 'Nazaj na spisek seznamov pogostih vprašanj';
$string['button:add_faq_title'] = 'Ustvari prevod naslova';
$string['button:edit_faq_title'] = 'Uredi prevod naslova';
$string['button:add_faq_item'] = 'Dodaj novo vprašanje';
$string['button:delete_faq_item'] = 'Izbriši';
$string['button:edit_faq_item'] = 'Uredi';

// Tables.
$string['label:col_shortname'] = 'Kratko ime';
$string['label:col_description'] = 'Opis';
$string['label:col_action_edit'] = 'Uredi';
$string['label:col_action_delete'] = 'Izbriši';
$string['label:col_faq_item'] = 'Vprašanje in odgovor';

// Messages.
$string['msg_faq_list_created'] = 'Seznam pogostih vprašanj je bil uspešno ustvarjen.';
$string['msg_faq_list_updated'] = 'Seznam pogostih vprašanj je bil uspešno posodobljen.';
$string['msg_faq_list_deleted'] = 'Seznam pogostih vprašanj je bil uspešno izbrisan.';
$string['msg_faq_list_not_exist'] = 'Seznam pogostih vprašanj ne obstaja.';
$string['msg_faq_title_created'] = 'Naslov seznama pogostih vprašanj uspešno narejen.';
$string['msg_faq_title_updated'] = 'Naslov seznama pogostih vprašanj uspešno posodobljen.';
$string['msg_faq_title_not_exist'] = 'Naslov seznama pogostih vprašanj ne obstaja.';
$string['msg_faq_item_created'] = 'Vprašanje in odgovor je bil uspešno ustvarjen.';
$string['msg_faq_item_updated'] = 'Vprašanje in odgovor je bil uspešno posodobljen.';
$string['msg_faq_item_deleted'] = 'Vprašanje in odgovor je bil uspešno izbrisan.';
$string['msg_faq_item_not_exist'] = 'Vprašanje ne obstaja.';

// View strings.


// Privacy.
$string['privacy:metadata'] = 'Vtičnik seznam pogostih vprašanj ne shranjuje nobenih osebnih uporabnikovih podatkov.';
