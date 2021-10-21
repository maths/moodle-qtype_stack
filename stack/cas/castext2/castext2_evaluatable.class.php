<?php
// This file is part of Stateful
//
// Stateful is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stateful is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.

require_once(__DIR__ . '/../evaluatable_object.interfaces.php');
require_once(__DIR__ . '/../cassecurity.class.php');
require_once(__DIR__ . '/castext2_static_replacer.class.php');
require_once(__DIR__ . '/utils.php');
require_once(__DIR__ . '/blocks/root.specialblock.php');

/**
 * A wrapper class encapsulating castext2 evaluation logic. Push one of
 * these in a cassession and be done with it. Once the sessio nhas been
 * evaluated one may ask for this to do the post-processing if you need it,
 * if you don't then some cycles have been saved.
 *
 * Supports use of pre-compiled castext for even faster operation. Also
 * includes logic for detecting if there is anything to evaluate at all
 * allowing one to easily see if this needs to be added to any session at
 * all. In such no evalaution required cases this will work jsut as well
 * as a handle for the castext content.
 */
class castext2_evaluatable implements cas_raw_value_extractor {

    private $compiled = null;
    private $source = null;
    private $value = null;
    private $evaluated = null;
    private $valid = null;
    private $errors = null;
    private $context = null;

    // Because we do not want to transfer large static strings to CAS we use a store that contains those values
    // and replace them into the result once eberything is complete.
    private $statics = null;

    public static function make_from_compiled(string $compiled, string $context, castext2_static_replacer $statics): castext2_evaluatable {
        $r = new castext2_evaluatable();
        $r->valid = true; // The compiled fragment is assumed to be validated.
        $r->compiled = $compiled;
        $r->context = $context;
        $r->statics = $statics;
        return $r;
    }


    public static function make_from_source(string $source, string $context): castext2_evaluatable {
        $r = new castext2_evaluatable();
        $r->source = $source;
        $r->context = $context;

        if ($source === '' || $source === null) {
            // This case is common enough to skip.
            $r->compiled = '""';
            $r->valid = true;
        }

        return $r;
    }

    private function __construct() {
        $this->errors = array();
    }

    // Format and options here are for the optional compilation.
    // Basically when compiling we need to know if Markdown is in use and
    // some blocks may need details. Note though that if you give this
    // Markdown or other types of formated stuff it will do the formating
    // and the rendered output will be FORMAT_HTML.
    public function get_valid($format=null, $options=null, $sec=null): bool {
        if ($this->valid !== null) {
            return $this->valid;
        }
        if ($sec === null) {
            $sec = new stack_cas_security();
        }
        $ast = null;
        switch ($format) {
            case FORMAT_HTML:
            case castext2_parser_utils::RAWFORMAT:
                // We do nothing to this.
                break;
            case FORMAT_MARKDOWN:
            case castext2_parser_utils::MDFORMAT:
                // We want to process it down to HTML.
                $this->source = '[[demarkdown]]' . $this->source . '[[/demarkdown]]';
                break;
            case FORMAT_MOODLE:
                // We want to process it down to HTML.
                $this->source = '[[demoodle]]' . $this->source . '[[/demoodle]]';
                break;
            case FORMAT_PLAIN:
                // TODO... We need to have something more complex for this
                // as the formating logic will need to also stop filtering for 
                // this. Check /lib/weblib.php in Moodle.
                break;
            default:
                $format = castext2_parser_utils::RAWFORMAT;
        }


        // If not already valid then not compiled either.
        try {
            $ast = castext2_parser_utils::parse($this->source);
        } catch (SyntaxError $e) {
            $this->valid = false;
            $this->errors = [$e->getMessage()];
            return false;
        }
        $root = stack_cas_castext2_special_root::make($ast);

        // Collect CAS statements.
        $css  = [];

        $err = [];
        $prtnames = [];
        if (isset($options['prt-names'])) {
            $prtnames = $options['prt-names'];
        }
        $valid = true;

        $collectstrings = function ($node) use (&$css, &$err, &$valid, $prtnames) {
            foreach ($node->validate_extract_attributes() as $cs) {
                $css[] = $cs;
            }
            // Also node specific validation.
            $valid = $node->validate($err, $prtnames) && $valid;
        };
        $this->errors = array_merge($this->errors, $err);
        $root->callbackRecurse($collectstrings);

        $this->valid = $valid;

        foreach ($css as $statement) {
            // Remember to check for security stuff here.
            $statement->set_securitymodel($sec);
            $this->valid = $this->valid && $statement->get_valid();
            if ($statement->get_errors()) {
                $this->errors = array_merge($this->errors, $statement->get_errors('array'));
            }
        }

        if ($this->valid) {
            $this->compiled = $root->compile($format, $options);
        }
        return $this->valid;
    }

    public function get_evaluationform(): string {
        if ($this->compiled === null) {
            if (!$this->get_valid()) {
                throw new stack_exception('trying to get evaluation form of invalid castext');
            }
        }
        return $this->compiled;
    }

    public function set_cas_status(array $errors, array $answernotes, array $feedback) {
        $this->errors = $errors;
        if (count($this->errors) > 0) {
            $this->valid = true;
        }
    }

    public function get_source_context(): string {
        return $this->context;
    }

    public function get_key(): string {
        return '';
    }

    public function set_cas_evaluated_value(string $stringval) {
        $this->value = $stringval;
    }

    public function requires_evaluation(): bool {
        if ($this->valid === null) {
            // Compile if not compiled.
            $this->get_valid();
        }
        if (mb_substr($this->compiled, 0, 1) === '"') {
            // If the compiled value is already a string this does not need
            // to go to the CAS for evaluation.
            $this->evaluated = stack_utils::maxima_string_to_php_string($this->compiled);
            return false;
        }
        return true;
    }

    public function get_rendered(castext2_processor $processor = null): string {
        if ($this->evaluated === null) {
            // Do the simpler parse of the value. The full MaximaParser
            // would obviously work but would be more expensive.
            // 
            // Note that pure strings are even simpler...
            if (mb_substr($this->value, 0, 1) === '"') {
                // If it evaluated to entirely flat result.
                $this->evaluated  = stack_utils::maxima_string_to_php_string($this->value);
                if ($this->statics !== null) {
                    $this->evaluated = $this->statics->replace($this->evaluated);
                }
            } else {
                $value = castext2_parser_utils::string_to_list($this->value, true);
                $value = castext2_parser_utils::unpack_maxima_strings($value);
                if ($this->statics !== null) { 
                    // This needs to happen before the postprocessing.
                    $value = $this->statics->replace($value);
                }
                $this->evaluated = castext2_parser_utils::postprocess_parsed($value, $processor);
            }
        }
        return $this->evaluated;
    }

    public function get_errors($implode = true) {
        if ($implode) {
            return implode(', ', $this->errors);
        }
        return $this->errors;
    }
}