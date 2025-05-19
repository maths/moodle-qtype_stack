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

require_once(__DIR__ . '/root.specialblock.php');
require_once(__DIR__ . '/stack_translate.specialblock.php');
require_once(__DIR__ . '/../../../../vle_specific.php');

require_once(__DIR__ . '/iframe.block.php');
stack_cas_castext2_iframe::register_counter('///HIGHLIGHTJS_COUNT///');

/**
 * A block using highlight.js to syntax highlight its contets.
 * 
 * The special thing is that the source material exists outside the JavaScript
 * executing iframe. The styling happens inside the iframe after which
 * CSS styling is written open and the inline styled result transferred back
 * out to replace the original content.
 * 
 * TODO: if this block becomes a thing we will need to include highlight.js
 * to the corsscripts so that this can work in closed installs.
 */
class stack_cas_castext2_highlightjs extends stack_cas_castext2_block {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {
        // At the very least we need question level running numbering, this is close enough.
        static $count = 0;
        $count = $count + 1;

        $r = new MP_List([new MP_String('iframe')]);

        // These will be hidden.
        $pars = ['hidden' => true];
        // Set a title.
        $pars['title'] = 'STACK highlight.js ///HIGHLIGHTJS_COUNT///';

        $r->items[] = new MP_String(json_encode($pars));


        $theme = 'default';
        if (isset($this->params['theme'])) {
            $theme = $this->params['theme'];
        }
        $theme = "https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/$theme.min.css";
        $r->items[] = new MP_List([
            new MP_String('style'),
            // NOTE later we are trying to read stylesheets from other domains and need that anonymous there.
            new MP_String(json_encode(['href' => $theme, 'crossorigin' => 'anonymous'])),
        ]);

        $js = "https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js";
        $r->items[] = new MP_List([
            new MP_String('script'),
            new MP_String(json_encode(['type' => 'text/javascript', 'src' => $js])),
        ]);

        $lang = 'maxima';
        if (isset($this->params['lang'])) {
            $lang = $this->params['lang'];
        }
        $langurl = "https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/languages/$lang.min.js";
        $r->items[] = new MP_List([
            new MP_String('script'),
            new MP_String(json_encode(['type' => 'text/javascript', 'src' => $langurl])),
        ]);

        // Start the script. We will always have type="module".
        $r->items[] = new MP_String('<div id="local">&nbsp;</div><script type="module">');

        // For binding and other use we need to import the stack_js library.
        $r->items[] = new MP_String("\nimport {stack_js} from '" . stack_cors_link('stackjsiframe.min.js') . "';\n");

        // A bit tricky but necessary for general case CSS application.
        // NOTE! If we were to include this block and would care about style transfer in general this would need to be added to the CORS-dir.
        $r->items[] = new MP_String("import cssSpecificityCalculator from 'https://cdn.jsdelivr.net/npm/css-specificity-calculator@2.1.1/+esm';\n");

        // We need the id for our code container.
        $r->items[] = new MP_String("\nconst codeelement = '");
        $r->items[] = new MP_List([new MP_String('quid'), new MP_String("stack_hljs_" . $count)]);
        $r->items[] = new MP_String("';\n");

        // TODO: This is a bit larger set of static-code, maybe part of it should be in stack_js.
        // First transfer the code from the VLE-side.
        // Then apply highlight.js.
        // Once applied convert CSS-rules to inline style, the same rules do not apply outside this iframe...
        // Then move the styled content to the VLE-side.
        $code = "stack_js.get_content(codeelement).then((content) => {
            let local = document.getElementById('local');
            local.innerHTML = content;

            hljs.highlightAll();

            /* Assuming no styles elsewhere than stylesheets. */
            /* Find all the rules that match any elements. */
            let rules = [];
            let specificitymap = {};

            for (const sheet of document.styleSheets) {
                for (const rule of sheet.cssRules) {
                    /* If only these were logically the same. */
                    /* There is now a risk that an attribute selector messes things up. */
                    if (rule.selectorText.includes(',')) {
                        for (const selector of rule.selectorText.split(',')) {
                            let trial = document.querySelector(selector.trim());
                            if (trial !== null) {
                                rules.push({'selectorText': selector.trim(), 'style': {'cssText': rule.style.cssText}});
                                specificitymap[selector.trim()] = cssSpecificityCalculator(selector.trim());
                            }                        
                        }
                    } else {
                        let trial = document.querySelector(rule.selectorText);
                        if (trial !== null) {
                            rules.push(rule);
                            specificitymap[rule.selectorText] = cssSpecificityCalculator(rule.selectorText);
                        }                        
                    }
                }
            }

            /* Now that we have all the rules that are relevant we can sort them by specificity. */
            /* Note that we assume ES2019 sort, as it is a stable sort and to us the declaration order matters. */
            rules.sort((a,b) => {
                const aspec = specificitymap[a.selectorText];
                const bspec = specificitymap[b.selectorText];
                if (aspec < bspec) {
                    return 1;
                } else if (aspec > bspec) {
                    return -1;
                }
                return 0;
            });

            /* Apply rules in reverse order, most specific first. */
            for (const rule of rules.reverse()) {
                const els = document.querySelectorAll(rule.selectorText);
                for (let i = 0; i < els.length; i++) {
                    let inlinestyle = els[i].getAttribute('style') || '';
                    /* Prepend the rule as inline always beats in specificity. */
                    inlinestyle = rule.style.cssText + ';' + inlinestyle;
                    /* TODO clean duplicates from that. */
                    els[i].setAttribute('style', inlinestyle);
                }
            }

            stack_js.switch_content(codeelement, local.innerHTML);
        });";
        
        $r->items[] = new MP_String($code);

        // In the end close the script tag.
        $r->items[] = new MP_String('</script>');

        // Now generate the contents. First move that iframe construct down.
        $r = new MP_List([new MP_String('%root'), $r]);

        // Then open a div to contain the code.
        $r->items[] = new MP_String("<div id='");
        $r->items[] = new MP_List([new MP_String('quid'), new MP_String("stack_hljs_" . $count)]);
        $r->items[] = new MP_String("'><pre><code class='hljs language-$lang'>");

        $ee = new MP_List([new MP_String('entityescape')]);
        // Dump the code into that div.
        foreach ($this->children as $item) {
            // Assume that all code inside is script and that we do not
            // want to do the markdown escaping or any other in it.
            $c = $item->compile(castext2_parser_utils::RAWFORMAT, $options);
            if ($c !== null) {
                $ee->items[] = $c;
            }
        }
        $r->items[] = $ee;
        $r->items[] = new MP_String("</code></pre></div>");

        return $r;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        // Even when the content were flat we need to evaluate this during postprocessing.
        return false;
    }


    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function postprocess(array $params, castext2_processor $processor,
        castext2_placeholder_holder $holder): string {
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
                    $err[] = stack_string('stackBlock_javascript_input_missing',
                        ['var' => $varname]);
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
        /* This block will display the code even when no syntax highlighting happens. */
        /* For pretext or similar processing this block should be rewriten as listings etc. */
        return false;
    }
}
