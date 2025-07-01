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
        $code .= "stack_js.register_external_button_listener('stack-repeatbutton-{$uid}', function() {";
        $code .= "add_repeat();";
        $code .= "});\n";
        $list[] = new MP_String($code);

		$list[] = new MP_String("window.repeat_counter = 0;");

		$list[] = new MP_String("function add_repeat(){\n");

		if (isset($this->params['repeat_ids'])) {
			$splitrepeatid = preg_split ("/[\ \n\;]+/", $this->params['repeat_ids']);
			foreach ($splitrepeatid as &$id) {
				$list[] = new MP_String("  const contentPromise_{$id} = stack_js.get_content('");
				$list[] = new MP_List([new MP_String('quid'), new MP_String("repeat_{$id}")]);
				$list[] = new MP_String("');\n");

				$list[] = new MP_String("  const containerPromise_{$id} = stack_js.get_content('");
				$list[] = new MP_List([new MP_String('quid'), new MP_String("repeatcontainer_{$id}")]);
				$list[] = new MP_String("');\n");

				$list[] = new MP_String("  Promise.all([contentPromise_{$id}, containerPromise_{$id}]).then(([repeat_content, repeatcontainer_content]) => {\n");
				$list[] = new MP_String("    window.repeat_counter++;\n");
				//$list[] = new MP_String("    console.log('repeat_counter: ',window.repeat_counter);\n");
				$list[] = new MP_String("    repeat_content = repeat_content.replace(/id=([\\\"'])(.*?)\\1/g, `id=$1repeat_{$id}_\${window.repeat_counter}_$2$1`);\n");
				$list[] = new MP_String("    repeat_content = repeat_content.replace(/name=([\\\"'])(.*?)\\1/g, `name=$1repeat_{$id}_\${window.repeat_counter}_$2$1`);\n");
				//$list[] = new MP_String("    console.log('repeat_id: {$id}');\n");
				//$list[] = new MP_String("    console.log('repeat_content:', repeat_content);\n");
				//$list[] = new MP_String("    console.log('repeatcontainer_content:', repeatcontainer_content);\n");
				$list[] = new MP_String("    stack_js.switch_content('");
				$list[] = new MP_List([new MP_String('quid'), new MP_String("repeatcontainer_{$id}")]);
				$list[] = new MP_String("', repeatcontainer_content + repeat_content);\n");
				$list[] = new MP_String("  });\n");
			}
		}

		$list[] = new MP_String("};");


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
