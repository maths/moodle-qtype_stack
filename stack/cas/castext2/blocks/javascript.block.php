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
//

/**
 * Add description here!
 * @package    qtype_stack
 * @copyright  2024 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../block.factory.php');
require_once(__DIR__ . '/../../parsingrules/parsingrule.factory.php');

require_once(__DIR__ . '/root.specialblock.php');
require_once(__DIR__ . '/stack_translate.specialblock.php');
require_once(__DIR__ . '/../../../../vle_specific.php');

require_once(__DIR__ . '/iframe.block.php');
stack_cas_castext2_iframe::register_counter('///JAVASCRIPT_COUNT///');

/**
 * A convenience block for creation of iframes with input-references and
 * stack_js pre-loaded. Use when you want to build some logic connected
 * to the input values.
 *
 * The script will run in `<script type="module">` so feel free to 'import'
 * things.
 *
 * Uses the same input-references declaration logic as [[jsxgraph]].
 */
class stack_cas_castext2_javascript extends stack_cas_castext2_block {
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {
        $r = new MP_List([new MP_String('iframe')]);

        $inputs = []; // From inputname to variable name.
        foreach ($this->params as $key => $value) {
            if (substr($key, 0, 10) === 'input-ref-') {
                $inputname = substr($key, 10);
                $inputs[$inputname] = $value;
            }
        }

        // These will be hidden.
        $pars = ['hidden' => true];
        // Set a title.
        $pars['title'] = 'STACK javascript ///JAVASCRIPT_COUNT///';

        $r->items[] = new MP_String(json_encode($pars));

        // Start the script. We will always have type="module".
        $r->items[] = new MP_String('&nbsp;<script type="module">');

        // For binding and other use we need to import the stack_js library.
        $r->items[] = new MP_String("\nimport stack_js from '" . stack_cors_link('stackjsiframe.min.js') . "';\n");

        // Process the contents.
        $content = [];
        $opt2 = [];
        if ($options !== null) {
            $opt2 = array_merge([], $options);
        }
        $opt2['in iframe'] = true;
        foreach ($this->children as $item) {
            // Assume that all code inside is JavaScript and that we do not
            // want to do the markdown escaping or any other in it.
            $c = $item->compile(castext2_parser_utils::RAWFORMAT, $opt2);
            if ($c !== null) {
                $content[] = $c;
            }
        }

        // Figure out if we have something else to import? But only if we add that Promise bit.
        if (count($inputs) > 0) {
            [$imports, $content] = self::separate_imports($content);
            if (count($imports) > 0) {
                $r->items = array_merge($r->items, $imports);
            }
        }

        // Do we need to bind anything?
        if (count($inputs) > 0) {
            // Then we need to link up to the inputs.
            $promises = [];
            $vars = [];
            foreach ($inputs as $key => $value) {
                // That true there makes us sync input-events as well, like we did before.
                $promises[] = 'stack_js.request_access_to_input("' . $key . '",true)';
                $vars[] = $value;
            }
            $linkcode = 'Promise.all([' . implode(',', $promises) . '])';
            $linkcode .= '.then(([' . implode(',', $vars) . ']) => {' . "\n";
            $r->items[] = new MP_String($linkcode);
        }

        // Include the content, previously compiled, within the possible Promise block.
        $r->items = array_merge($r->items, $content);


        if (count($inputs) > 0) {
            // Close the `then(`.
            $r->items[] = new MP_String("\n});");
        }

        // In the end close the script tag.
        $r->items[] = new MP_String('</script>');

        return $r;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        // Even when the content were flat we need to evaluate this during postprocessing.
        return false;
    }


    /**
     * Takes a sequence of already compiled CASText that is assumed to produce 
     * JavaScript syntax and tries to identify import statements from within it.
     * Returns the imports and the rest of the content separated, into lists.
     * 
     * For use with generated promise logic like the `input-ref-...` shorthand.
     * 
     * Note! Not currently aware of JS comments.
     */
    public static function separate_imports(array $compiledcontent): array {
        // First simplify the input. To deal with various static things like [[CORS]] urls.
        $root = [new MP_String('%root')];
        $root = new MP_Root([new MP_List(array_merge($root, $compiledcontent))]);
        $simplificationfilter = stack_parsing_rule_factory::get_by_common_name('602_castext_simplifier');
        $notused1 = [];
        $notused2 = [];
        $root = $simplificationfilter->filter($root, $notused1, $notused2, new stack_cas_security());

        // Unwrap back to an array.
        $content = $root->items[0];
        if ($content instanceof MP_String) {
            $content = [$content];
        } else {
            $content = $content->items;
            // Drop the '%root';
            array_shift($content);
        }

        // Then start splitting the content.
        $imports = [];
        $remainder = [];

        $lookingforsemicolon = false;
        while (count($content) > 0) {
            $focus = array_shift($content);
            $focus->parentnode = null;
            if ($focus instanceof MP_String) {
                if ($lookingforsemicolon) {
                    if (mb_strpos($focus->value, ';') === false) {
                        // This is an unlikely case but for completenes sake.
                        $imports[] = $focus;
                    } else {
                        $i = mb_strpos($focus->value, ';');
                        $uptoand = mb_substr($focus->value, 0, $i + 1);
                        $remains = mb_substr($focus->value, $i + 1);
                        $imports[] = new MP_String($uptoand . "\n");
                        $lookingforsemicolon = false;
                        // Return for further processing.
                        array_unshift($content, new MP_String($remains));
                    }
                } else {
                    // Not looking for a semicolon, so looking for the import.
                    // Note that we do not handle all matches. Only the first one.
                    // First do we have a full import?
                    $matches = [];
                    preg_match('/(^|[;^\s])(import [^;]+;)/', $focus->value, $matches);
                    $full = null;
                    if (count($matches) > 0) {
                        $full = $matches[2];
                    }
                    // Then the case of a start.
                    $matches = [];
                    preg_match('/(^|[;^\s])(import\s)/', $focus->value, $matches);
                    $start = null;
                    if (count($matches) > 0) {
                        $start = $matches[2];
                    }

                    // Which is first?
                    if ($full !== null && (mb_strpos($focus->value, $full) === mb_strpos($focus->value, $start))) {
                        // The first one is the full so we simply slice that out and continue.
                        $imports[] = new MP_String($full . "\n");
                        $focus->value = str_replace($full, ' ', $focus->value);
                        array_unshift($content, $focus);
                    } else if ($start !== null) {
                        // So we only have the start of the import.
                        $lookingforsemicolon = true;
                        $i = mb_strpos($focus->value, $start);
                        $upto = mb_substr($focus->value, 0, $i);
                        $remains = mb_substr($focus->value, $i);
                        $imports[] = new MP_String($remains);
                        $remainder[] = new MP_String($upto);
                    } else {
                        // Nothing.
                        $remainder[] = $focus;
                    }
                }
            } else {
                if ($lookingforsemicolon) {
                    $imports[] = $focus;
                } else {
                    $remainder[] = $focus;
                }
            }
        }

        return [$imports, $remainder];
    }


    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function postprocess(
        array $params,
        castext2_processor $processor,
        castext2_placeholder_holder $holder
    ): string {
        return 'This is never happening! The logic goes to [[iframe]].';
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate_extract_attributes(): array {
        return [];
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate(
        &$errors = [],
        $options = []
    ): bool {
        $valid  = true;
        $err = [];
        $valids = null;
        foreach ($this->params as $key => $value) {
            if (substr($key, 0, 10) === 'input-ref-') {
                $varname = substr($key, 10);
                if (isset($options['inputs']) && !isset($options['inputs'][$varname])) {
                    $err[] = stack_string(
                        'stackBlock_javascript_input_missing',
                        ['var' => $varname]
                    );
                }
            }
        }

        // Wrap the old string errors with the context details.
        foreach ($err as $er) {
            $errors[] = new $options['errclass']($er, $options['context'] . '/' . $this->position['start'] . '-' .
                $this->position['end']);
        }

        return $valid;
    }

    /**
     * Is this an interactive block?
     * If true, we can't generate a static version.
     * @return bool
     */
    public function is_interactive(): bool {
        return true;
    }
}
