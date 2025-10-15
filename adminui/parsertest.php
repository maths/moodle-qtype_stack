<?php
// This file is part of Stack - https://www.ed.ac.uk/maths/stack/
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

// TODO: do we keep this? will it be linked somewhere?

/**
 * Tester for the new parser
 * @package    qtype_stack
 * @copyright  2025 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/cas/parsingrules/parsingrule.factory.php');
require_once(__DIR__ . '/../stack/maximaparser/parser.options.class.php');
require_once(__DIR__ . '/../stack/maximaparser/error.interpreter.class.php');

require_login();
$PAGE->set_context(context_system::instance());

// First the AJAX portion.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = json_decode(file_get_contents('php://input'), true);
    $result = [];
    header("Content-type: application/json; charset=utf-8");

    $basen = false;
    $commas = StackLexerSeparators::Dot;
    if (isset($json['separators']) && $json['separators'] === 'comma') {
        $commas = StackLexerSeparators::Comma;
    }
    if (isset($json['separators']) && $json['separators'] === 'basen') {
        $basen = true;
    }

    $insertion = StackParserInsertionOption::Stars;
    if (isset($json['insert'])) {
        if ($json['insert'] === 'stars') {
            $insertion = StackParserInsertionOption::Stars;
        } else if ($json['insert'] === 'none') {
            $insertion = StackParserInsertionOption::None;
        } else if ($json['insert'] === 'endtoken') {
            $insertion = StackParserInsertionOption::EndToken;
        }
    }

    $rule = StackParserRule::Root;
    if (isset($json['rule'])) {
        if ($json['rule'] === 'root') {
            $rule = StackParserRule::Root;
        } else if ($json['rule'] === 'equivline') {
            $rule = StackParserRule::Equivline;
        }
    }

    // Start from something like old default student parsing.
    $options = stack_parser_options::get_old_config();
    $options->tryinsert = $insertion;
    $options->separators = $commas;
    $options->rule = $rule;
    $options->basen = $basen;

    $input = '';
    if (isset($json['input'])) {
        $input = $json['input'];
    }

    // Lexer.
    $lexer = $options->get_lexer($input);
    // Generate a list of tokens.
    $tokens = [];
    $i = $lexer->get_next_token();
    while ($i !== null) {
        $tokens[] = $i;
        $i = $lexer->get_next_token();
    }
    $lexer->reset();
    $result['tokens'] = $tokens;

    // Parse the thing.
    $result['debugprint'] = null;
    $result['debugprint_filtered'] = null;
    $result['answernotes'] = null;
    $result['reproduction'] = null;
    $result['tocas'] = null;
    $parser = $options->get_parser();
    $r = null;
    try {
        $errors = [];
        $anotes = [];
        $r = $parser->parse($lexer, $anotes);
        $result['debugprint'] = $r->debugPrint($input);
        $extrafilters = $options->get_ast_filters();
        $pipeline = stack_parsing_rule_factory::get_filter_pipeline($extrafilters[0], $extrafilters[1], true);
        $r = $pipeline->filter($r, $errors, $anotes, new stack_cas_security());
        $result['debugprint_filtered'] = $r->debugPrint($input);
        $result['answernotes'] = $anotes;
        $result['reproduction'] = $r->toString($options->get_to_string_settings());
        $result['tocas'] = $r->toString();
    } catch (stack_maxima_parser_exception $e) {
        $errors = [];
        $notes = [];
        $ei = new stack_parser_error_interpreter($options);
        $result['exception'] = $e;
        $result['interpreted'] = $ei->interprete($e, $errors, $notes);
        $result['errors'] = $errors;
        $result['error_notes'] = $notes;
    }

    echo json_encode($result);
    exit();
}


$PAGE->set_url('/question/type/stack/parsertest.php');
$title = stack_string('parsertester');
$PAGE->set_title($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);

echo $OUTPUT->heading(stack_string('parsertester_settings'), 3);

echo '<table id="parsertest_container"><tr><th>' . stack_string('parsertester_settings_rule');
echo '</th><th>' . stack_string('parsertester_settings_separators');
echo '</th><th>' . stack_string('parsertester_settings_insert');
echo '</th></tr><tr>';

// Row at a time...

echo '<td><input type="radio" name="rule" id="rule_root" value="root" checked/>';
echo '<label for="rule_root">Root parser</label></td>';

echo '<td><input type="radio" name="separators" id="separators_dot" value="dot" checked/>';
echo '<label for="separators_dot">Decimal dot, list comma, statement ; or $</label></td>';

echo '<td><input type="radio" name="insert" id="insert_stars" value="stars" checked/>';
echo '<label for="insert_stars">Insert stars</label></td>';

echo '</tr><tr>';

echo '<td><input type="radio" name="rule" id="rule_equivline" value="equivline"/>';
echo '<label for="rule_equivline">EquivLine parser</label></td>';

echo '<td><input type="radio" name="separators" id="separators_comma" value="comma"/>';
echo '<label for="separators_comma">Decimal comma, list ;, statement $</label></td>';

echo '<td><input type="radio" name="insert" id="insert_none" value="none"/>';
echo '<label for="insert_none">Insert nothing</label></td>';

echo '</tr><tr>';

echo '<td>&nbsp;</td>';

echo '<td><input type="radio" name="separators" id="separators_basen" value="basen"/>';
echo '<label for="separators_basen">Base-N, lists , or ;, statement $. Note, no decimal numbers of any kind.</label></td>';

echo '<td><input type="radio" name="insert" id="insert_endtoken" value="endtoken"/>';
echo '<label for="insert_endtoken">Insert statement separator</label></td>';


echo '</tr></table>';

echo $OUTPUT->heading(stack_string('parsertester_input'), 3);

echo '<textarea rows="20" cols="80" id="testinput">[1,2];simp:true</textarea>';

echo $OUTPUT->heading(stack_string('parsertester_output'), 3);

echo '<table><tr><th>Parse result</th><th>Error</th></tr>';
echo '<tr><td style="vertical-align:top;"><pre id="parse_result"></pre></td><td style="vertical-align:top;"><pre id="parse_error"></pre></td></tr>';

echo '<tr><th colspan="2">Reproduction</th></tr>';
echo '<tr><td colspan="2" style="vertical-align:top;"><pre id="parse_reproduction"></pre></td></tr>';

echo '<tr><th>Lexer result</th><th>Exception</th></tr>';
echo '<tr><td style="vertical-align:top;"><pre id="lexer_result"></pre></td><td style="vertical-align:top;"><pre id="parse_exception"></pre></td></tr>';

echo '</table>';

?>
<script>
const send = () => {
    let request = new Request('parsertest.php', {
        method: 'POST',
        body: JSON.stringify({
            insert: document.querySelector('input[name=insert]:checked').value,
            rule: document.querySelector('input[name=rule]:checked').value,
            separators: document.querySelector('input[name=separators]:checked').value,
            input: document.getElementById('testinput').value
        })
    });
    fetch(request).then((response) => response.json()).then((data) => {
        document.getElementById('lexer_result').innerHTML = JSON.stringify(data['tokens'],null,1);
        if (data['debugprint'] === data['debugprint_filtered']) {
            document.getElementById('parse_result').innerHTML = data['debugprint'] + "\nDid not change in core or parser filters.\n\nAnswernotes: " + JSON.stringify(data['answernotes']);
        } else {
            document.getElementById('parse_result').innerHTML = data['debugprint'] + "\n\nAfter core and parser filters:\n" + data['debugprint_filtered'] + "\n\nAnswernotes: " + JSON.stringify(data['answernotes']);
        }
        document.getElementById('parse_error').innerHTML = data['interpreted'] + "\n\Answernotes: "  + JSON.stringify(data['error_notes']) + "\n\nSeparated errors: " + JSON.stringify(data['errors']);
        document.getElementById('parse_exception').innerHTML = JSON.stringify(data['exception'],null,1);
        document.getElementById('parse_reproduction').innerHTML = data['reproduction'] + "\n\nTo CAS version:\n" + data['tocas'];
    });
};

let thandle = null;
const dedup = () => {
    if (thandle !== null) {
        clearTimeout(thandle);
    }
    thandle = setTimeout(() => {
        send();
    },200);
};

document.querySelectorAll('#parsertest_container input[type=radio]').forEach((e) => {
    e.addEventListener('click', () => send());
});

document.getElementById('testinput').addEventListener('input', dedup);


send();
</script>
<?php


echo $OUTPUT->footer();
