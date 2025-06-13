<?php
// This file is part of STACK
//
// STACK is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// STACK is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This class adds in the interactive repeat button blocks to castext.
 * @package    qtype_stack
 * @copyright  2025 University of Edinburgh.
 * @copyright  2025 Ruhr University Bochum.
 * @copyright  2025 ETH Zürich.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../block.interface.php');
// Register a counter.
require_once(__DIR__ . '/iframe.block.php');
stack_cas_castext2_iframe::register_counter('///REPEATBUTTON_COUNT///');

/**
 * This class adds in the repeat button blocks to castext.
 */
class stack_cas_castext2_repeatbutton extends stack_cas_castext2_block {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {

        // All reveals need unique (at request level) identifiers, we use running numbering.
        static $count = 0;

        $body = new MP_List([new MP_String('%root')]);

        // This should have enough randomness to avoid collisions.
        $uid = '' . rand(100, 999) . time() . '_' . $count;
        $count = $count + 1;

        $body->items[] = new MP_String('<button type="button" class="btn btn-secondary" id="stack-repeatbutton-' .
            $uid . '">' . $this->params['title'] . '</button>');

        $list = [];
        $list[] = new MP_String('script');
        $list[] = new MP_String(json_encode(['type' => 'module']));

        $code = "\nimport {stack_js} from '" . stack_cors_link('stackjsiframe.min.js') . "';\n";
        $code .= "stack_js.request_access_to_input('" . $this->params['save_state'] . "', true).then((id) => {\n";
		$code .= "const state_store = document.getElementById(id);\n";
        $code .= "if (state_store.value!=''){ console.log('we need to reconstruct'); }\n";   // TODO: we need to build a js function for reconstruction the inputs
        $code .= "});\n";
        $code .= "stack_js.register_external_button_listener('stack-repeatbutton-". $uid . "', function() {";
        $code .= "add_repeat();";
        $code .= "});\n";
        

        $list[] = new MP_String($code);

		$splitrepeatid = preg_split("/[\ \n\;]+/", $this->params['repeat_ids'] ?? '');
		$code = "";

		$code .= <<<JS
		function getSaveState() {
		  const raw = state_store.value;
		  return raw ? JSON.parse(raw) : {
			num_copies: 1,
			data: [
				{ repeat_id: '$id', inputs: { } }
			]
		  };
		}

		function setSaveState(state) {
		  state_store.value = JSON.stringify(state);
		}

		function updateSaveStateInput(repeat_id, name, index, value) {
		  const state = getSaveState();
		  const block = state.data.find(b => b.repeat_id === repeat_id);
		  if (!block) return;
		  if (!block.inputs[name]) block.inputs[name] = [];
		  while (block.inputs[name].length <= index) {
			block.inputs[name].push('');
		  }
		  block.inputs[name][index] = value;
		  setSaveState(state);
		}

		function add_repeat() {
		  const state = getSaveState();
		  const newIndex = state.num_copies;
		  state.num_copies++;

		JS;

		foreach ($splitrepeatid as $id) {
			$contentKey = "'quid' + 'repeat_$id'";
			$targetId = "'quid' + 'repeatcontainer_$id'";
			$code .= <<<JS
		  {
			const contentPromise = stack_js.get_content($contentKey);
			contentPromise.then((html) => {
			  const temp = document.createElement('div');
			  temp.innerHTML = html;

			  temp.querySelectorAll('[id]').forEach(el => {
				el.id = el.id + '_' + newIndex;
			  });

			  temp.querySelectorAll('[name]').forEach(el => {
				const originalName = el.name;
				const newName = originalName + '_' + newIndex;
				el.name = newName;
				el.setAttribute('data-repeat-index', newIndex);
				el.value = '';
				el.addEventListener('input', () => updateSaveStateInput('$id', originalName, newIndex, el.value));
			  });

			  const newHTML = temp.innerHTML;
			  const container = document.getElementById($targetId);
			  container.innerHTML += newHTML;

			  const block = state.data.find(b => b.repeat_id === '$id');
			  if (block) {
				for (const key in block.inputs) {
				  block.inputs[key].push('');
				}
			  }

			  setSaveState(state);
			});
		  }

		JS;
		}

		$code .= <<<JS
		}
		document.addEventListener('DOMContentLoaded', () => {
		JS;

		foreach ($splitrepeatid as $id) {
			$selector = "'#' + 'quid' + 'repeat_$id' + ' input'";
			$code .= <<<JS
		  document.querySelectorAll($selector).forEach((input) => {
			const name = input.name;
			input.setAttribute('data-repeat-index', 0);
			input.addEventListener('input', () => updateSaveStateInput('$id', name, 0, input.value));
		  });

		JS;
		}

		$code .= <<<JS
		  setSaveState(getSaveState());
		});
		JS;

		$list[] = new MP_String($code);



        // Now add a hidden [[iframe]] with suitable scripts.
        $body->items[] = new MP_List([
            new MP_String('iframe'),
            new MP_String(json_encode([
                'hidden' => true,
                'title' => 'Logic container for a repeatbutton  ///REPEATBUTTON_COUNT///.',
            ])),
            new MP_List($list),
        ]);

        return $body;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        return true;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function postprocess(array $params, castext2_processor $processor, castext2_placeholder_holder $holder): string {
        return 'Post processing of repeatbutton blocks never happens, this block is handled through [[iframe]].';
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate_extract_attributes(): array {
        $r = [];
        if (!isset($this->params['title'])) {
            return $r;
        }
        if (!isset($this->params['repeat_ids'])) {
            return $r;
        }
        if (!isset($this->params['save_state'])) {
            return $r;
        }
        return $r;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('title', $this->params)) {
            $errors[] = new $options['errclass']('Repeatbutton block requires a title parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }

        if (trim($this->params['title']) === '') {
            $errors[] = new $options['errclass']('Repeatbutton block title must be non-empty.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }

        if (!array_key_exists('repeat_ids', $this->params)) {
            $errors[] = new $options['errclass']('Repeatbutton block requires a repeat_ids parameter.',
                $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        if (!array_key_exists('save_state', $this->params)) {
            $errors[] = new $options['errclass']('Repeatbutton block requires a save_state parameter.',
                $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        return true;
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
