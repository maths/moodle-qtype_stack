<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This script lets the user see the latest commands stored to CAS
 * cache as pretty printted presentations. This exists for debugging.
 *
 * @copyright  2019 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/utils.class.php');
require_once(__DIR__ . '/stack/options.class.php');
require_once(__DIR__ . '/stack/maximaparser/utils.php');


require_login();
$context = context_system::instance();
require_capability('qtype/stack:usediagnostictools', $context);

$urlparams = array();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/caschat.php', $urlparams);
$title = 'Cache debug';
$PAGE->set_title($title);

// Get the ten latest cached things.
$data = $DB->get_recordset_sql('SELECT * FROM {qtype_stack_cas_cache} ORDER BY id DESC LIMIT 10;');

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$i = 0;
foreach ($data as $item) {
    $i++;
    echo '<h2>item ' . $i . '</h2>';
    echo '<pre>';
    // Ends with $ which is bad.
    // Now can contain $ signs elsewhere as well...
    $ast = maxima_parser_utils::parse(str_replace('$', ';', $item->command));
    $str = $ast->toString(array('pretty' => true));
    $str = str_replace('&', '&amp;', $str);
    $str = str_replace('<', '&lt;', $str);
    $str = str_replace('>', '&gt;', $str);
    echo $str;
    echo '</pre>';
    echo '<pre>';
    $json = json_decode($item->result);
    $str = json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    $str = str_replace('&', '&amp;', $str);
    $str = str_replace('<', '&lt;', $str);
    $str = str_replace('>', '&gt;', $str);
    echo $str;
    echo '</pre>';
}

echo $OUTPUT->footer();
