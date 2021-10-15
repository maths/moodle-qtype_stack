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

defined('MOODLE_INTERNAL')|| die();

require_once(__DIR__ . '/cas/evaluatable_object.interfaces.php');
require_once(__DIR__ . '/cas/castext2/utils.php');

/**
 * A wrapper class encapsulating PRT-evaluation logic. Just add
 * to the session after the inputs and the declarations,
 * especially the declaration of the matching PRT-function and
 * this will parse the output into easily usable forms.
 *
 * This is not entirely unlike the old PRT-state class.
 */
class prt_evaluatable implements cas_raw_value_extractor {

	// The function to call.
	private $signature;

	// The generated feedback.
	private $feedback = null;
	// Render the castext only if specifically asked.
	private $renderedfeedback = null;

	// The generated path.
	private $path = null;

    // Node notes, i.e. not the test notes.
    private $notes = null;

	// The generated score.
	private $score = null;

	// The generated penalty.
	private $penalty = null;

	// The value from CAS.
	private $evaluated = null;

	// Cas errors.
	private $errors;

	private $weight = 1;

    public function __construct(string $signature, $weight = 1) {
    	$this->signature = $signature;
    	$this->weight = $weight;
        $this->errors = [];
    }


    public function set_cas_evaluated_value(string $value) {
    	$this->evaluated = $value;
    }

    public function get_valid(): bool {
    	return count($this->errors) === 0;
    }

    public function get_evaluationform(): string {
    	return $this->signature;
    }

    public function set_cas_status(array $errors, array $answernotes, array $feedback) {
        $this->errors = $errors;
    }

    public function get_source_context(): string {
    	// Assume the signature has the PRT-name... and use it.
    	return explode('(',substr($this->signature, 4))[0];
    }

    public function get_key(): string {
    	return '';
    }

    public function is_evaluated(): bool {
    	return $this->evaluated !== null;
    }

    // Some spaghetti. TODO: eliminate
    public function override_feedback(string $feedback) {
    	$this->_feedback = 'spaghetti';
    	$this->renderedfeedback = $feedback;
    }

    private function unpack() {
        if (!$this->is_evaluated()) {
            return; // Cannot do this.
        }
        // Do the simpler parse of the value. The full MaximaParser
        // would obviously work but would be more expensive.
        $value = castext2_parser_utils::string_to_list($this->evaluated, true);
        if (count($value) < 4) {
            return;
        }
        $this->path      = $value[0];
        $this->score     = stack_utils::fix_to_continued_fraction($value[1], 4);
        $this->penalty   = stack_utils::fix_to_continued_fraction($value[2], 4);
        $this->feedback  = $value[3];
        $this->notes     = $value[4];
    }

    public function get_score() {
    	if ($this->score === null) {
    		$this->unpack();
    	}
    	return $this->score;
    }

    public function get_fraction() {
    	return $this->weight * $this->get_score();
    }

    public function get_fractionalpenalty() {
    	return $this->weight * $this->get_penalty();
    }


    public function get_penalty() {
    	if ($this->penalty === null) {
    		$this->unpack();
    	}
        // The penalty is 0 if the score is 1. No matter what.
        if ($this->score == 1) {
            return 0;
        }

    	return $this->penalty;
    }

    public function get_path() {
    	if ($this->path === null) {
    		$this->unpack();
    	}
    	return $this->path;
    }

    public function get_feedback($processor=null) {
        if (!$this->is_evaluated()) {
            // If not procesed return undefined or any overrides.
            return $this->renderedfeedback;
        }
    	if ($this->feedback === null) {
    		$this->unpack();
    	}
        if ($this->feedback === null) {
            return null;
        }
        if ($this->renderedfeedback === null) {
            // Note that pure strings are even simpler...
            if (is_string($this->feedback)) {
                // If it was flat.
                $this->renderedfeedback  = stack_utils::maxima_string_to_php_string($this->feedback);
            } else {
                $value = castext2_parser_utils::unpack_maxima_strings($this->feedback);
                $this->renderedfeedback = castext2_parser_utils::postprocess_parsed($value, $processor);
            }
            
        }
    	return $this->renderedfeedback;
    }

    public function get_answernotes() {
    	$path = $this->get_path();
    	$notes = [];
        if ($path === null || !is_array($path)) {
            return $notes;
        }
        $i = 0;
    	foreach ($path as $atresult) {
    		if ($atresult[2] !== '""') {
    			$notes[] = $atresult[2];
    		}
            if ($this->notes[$i] !== '""') {
                $notes[] = $this->notes[$i];
            }
            $i = $i + 1;
    	}
        // Note at this point those values are still Maxima string so unwrap them.
        for ($i = 0; $i < count($notes); $i++) {
            $notes[$i] = stack_utils::maxima_string_to_php_string($notes[$i]);
        }

    	return $notes;
    }

    public function get_errors() {
        // Apparently one wants to separate feedback-var errors?
    	$err = [];
        foreach ($this->errors as $er) {
            if (strpos($er, ': feedback-variables') === false) {
                $err[] = $er;
            }
        }
        return $err;
    }

    public function get_fverrors() {
        $err = [];
        foreach ($this->errors as $er) {
            if (strpos($er, ': feedback-variables') !== false) {
                // If these are all for FV we can drop the prefix.
                $err[] = explode(': feedback-variables', $er)[1];
            }
        }
        return $err;
    }

    public function get_trace(): array {
        // TODO: Do we need to generate feedback-vars? Jsut answer test results?
        // Would we need to get the inputs to those tests as well? This woudl 
        // require some extra logic to be evaluated. Or an addition of complex
        // additional output to the compiled function.
        return ['TODO TRACE'];
    }

    public function get_debuginfo(): string {
        return 'TODO DEBUGINFO';
    }
}