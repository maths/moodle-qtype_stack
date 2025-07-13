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

		$stackjs_url = stack_cors_link('stackjsiframe.min.js');
		$save_state = $this->params['save_state'];

		$code = <<<JS
		import {stack_js} from '{$stackjs_url}';
		stack_js.request_access_to_input('{$save_state}', true).then((id) => {
			let input = document.getElementById(id);
			input.addEventListener('change', function(){
				console.log('input save_state', id, this.value);
			});
			window.save_state = id;
			console.log('starte init_repeat');
			init_repeat();
		});
		stack_js.register_external_button_listener('stack-repeatbutton-{$uid}', function() {
			add_repeat();
		});
		JS;

		$list[] = new MP_String($code);

		$list[] = new MP_String("window.repeat_counter = 0;");

		// function init_repeat()
		$list[] = new MP_String("function init_repeat(){\n");

		if (isset($this->params['repeat_ids'])) {
			$splitrepeatid = preg_split ("/[\ \n\;]+/", $this->params['repeat_ids']);
			foreach ($splitrepeatid as &$id) {
				$list[] = new MP_String("let repeat_id='");
				$list[] = new MP_List([new MP_String('quid'), new MP_String("repeat_{$id}")]);
				$list[] = new MP_String("';\n");

				$code = <<<JS
				const contentPromise_{$id} = stack_js.get_content(repeat_id);
				contentPromise_{$id}.then((repeat_content) => {

					const tempContainer = document.createElement('div');
					tempContainer.innerHTML = repeat_content;
					
					const input_ids = [...tempContainer.querySelectorAll('input')].map(input => input.id);
					
					window.repeat_input_ids = window.repeat_input_ids || {};
					window.repeat_input_ids[{$id}] = input_ids;

					console.log('Mit DOM gefundene IDs für {$id}:', input_ids);
					console.log('Alle gespeicherten IDs:', window.repeat_input_ids);
				});

				JS;

				$list[] = new MP_String($code);
			}
		}

		$list[] = new MP_String("};");
		// end function init_repeat()

		// function add_repeat()
		$list[] = new MP_String("function add_repeat(){\n");

		if (isset($this->params['repeat_ids'])) {
			$splitrepeatid = preg_split ("/[\ \n\;]+/", $this->params['repeat_ids']);
			foreach ($splitrepeatid as &$id) {
				$list[] = new MP_String("let repeat_id='");
				$list[] = new MP_List([new MP_String('quid'), new MP_String("repeat_{$id}")]);
				$list[] = new MP_String("';\n");

				$list[] = new MP_String("let repeatcontainer_id='");
				$list[] = new MP_List([new MP_String('quid'), new MP_String("repeatcontainer_{$id}")]);
				$list[] = new MP_String("';\n");

				$code = <<<JS
				const contentPromise_{$id} = stack_js.get_content(repeat_id);
				const containerPromise_{$id} = stack_js.get_content(repeatcontainer_id);

				Promise.all([contentPromise_{$id}, containerPromise_{$id}]).then(([repeat_content, repeatcontainer_content]) => {
					window.repeat_counter++;
					repeat_content = repeat_content.replace(/id=([\\\"'])(.*?)\\1/g, `id=\$1repeat_{$id}_\${window.repeat_counter}_\$2\$1`);
					repeat_content = repeat_content.replace(/name=([\\\"'])(.*?)\\1/g, `name=\$1repeat_{$id}_\${window.repeat_counter}_\$2\$1`);
					//console.log('repeat_content 2:', repeat_content);
					//console.log('window.save_state:', document.getElementById(window.save_state).value);
					const switchPromise = stack_js.switch_content(repeatcontainer_id, repeatcontainer_content + repeat_content);
				});
				JS;

				$list[] = new MP_String($code);
			}
		}

		$list[] = new MP_String("};");
		// end function add_repeat()
		


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
        if (!array_key_exists('title', $this->params) || trim($this->params['title']) === '') {
            $errors[] = new $options['errclass']('Repeatbutton block requires a non-empty title parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        if (!array_key_exists('repeat_ids', $this->params) || trim($this->params['repeat_ids']) === '') {
            $errors[] = new $options['errclass']('Repeatbutton block requires a non-empty repeat_ids parameter.',
                $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        if (!array_key_exists('save_state', $this->params) || trim($this->params['save_state']) === '') {
            $errors[] = new $options['errclass']('Repeatbutton block requires a non-empty save_state parameter.',
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
